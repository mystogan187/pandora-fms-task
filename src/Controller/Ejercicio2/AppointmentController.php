<?php

namespace App\Controller\Ejercicio2;

use App\Ejercicio2\Entity\Patient;
use App\Ejercicio2\Entity\Appointment;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Lock\Store\FlockStore;

class AppointmentController extends AbstractController
{
    #[Route(['/', '/appointment'], name: 'appointment_form')]
    public function index(): Response
    {
        return $this->render('appointment/form.html.twig');
    }

    #[Route('/appointment/check-dni', name: 'check_dni', methods: ['GET'])]
    public function checkDni(Request $request, EntityManagerInterface $em): Response
    {
        $dni = $request->query->get('dni');

        $patient = $em->getRepository(Patient::class)->findOneBy(['dni' => $dni]);

        return $this->json([
            'exists' => $patient !== null
        ]);
    }

    #[Route('/appointment/submit', name: 'appointment_submit', methods: ['POST'])]
    public function submit(Request $request, EntityManagerInterface $em, MailerInterface $mailer): Response
    {
        $dni = $request->request->get('dni');
        $name = $request->request->get('name');
        $phone = $request->request->get('phone');
        $email = $request->request->get('email');
        $type = $request->request->get('type');

        $store = new FlockStore();
        $factory = new LockFactory($store);
        $lock = $factory->createLock('appointment_assignment');

        if (!$lock->acquire()) {
            return new Response('No se pudo adquirir el lock', 500);
        }

        try {
            $em->beginTransaction();
            $patient = $em->getRepository(Patient::class)->findOneBy(['dni' => $dni]);

            if (!$patient) {
                $patient = new Patient();
                $patient->setDni($dni);
                $patient->setName($name);
                $patient->setPhone($phone);
                $patient->setEmail($email);

                $em->persist($patient);
                $em->flush();
            }

            $appointment = new Appointment();
            $appointment->setPatient($patient);
            $appointment->setType($type);

            $appointmentDateTime = $this->getNextAvailableSlot($em);
            $appointment->setDateTime($appointmentDateTime);

            $em->persist($appointment);
            $em->flush();

            $em->commit();

            $lock->release();

            $this->sendAppointmentEmail($mailer, $patient, $appointment);

            return $this->render('appointment/success.html.twig', [
                'appointment' => $appointment
            ]);

        } catch (\Exception $e) {
            $em->rollback();
            $lock->release();
            return new Response('Error al procesar la cita', 500);
        }
    }

    private function getNextAvailableSlot(EntityManagerInterface $em): \DateTime
    {
        $startDate = new \DateTime();
        $startDate->setTime(10, 0);

        $endTime = 22;

        while (true) {
            for ($hour = $startDate->format('H'); $hour < $endTime; $hour++) {
                $dateTime = clone $startDate;
                $dateTime->setTime($hour, 0);

                $existingAppointment = $em->getRepository(Appointment::class)
                    ->findOneBy(['dateTime' => $dateTime]);

                if (!$existingAppointment) {
                    if ($this->hasGapsBefore($em, $dateTime)) {
                        continue;
                    }
                    return $dateTime;
                }
            }

            $startDate->modify('+1 day');
            $startDate->setTime(10, 0);
        }
    }

    private function hasGapsBefore(EntityManagerInterface $em, \DateTime $dateTime): bool
    {
        $prevHour = clone $dateTime;
        $prevHour->modify('-1 hour');

        if ((int)$prevHour->format('H') < 10) {
            return false;
        }

        $existingAppointment = $em->getRepository(Appointment::class)
            ->findOneBy(['dateTime' => $prevHour]);

        return !$existingAppointment;
    }

    private function sendAppointmentEmail(MailerInterface $mailer, Patient $patient, Appointment $appointment)
    {
        $email = (new Email())
            ->from('aec.alexandru@gmail.com')
            ->to($patient->getEmail())
            ->subject('Detalles de su Cita')
            ->html($this->renderView('emails/appointment.html.twig', [
                'patient' => $patient,
                'appointment' => $appointment
            ]));

        try {
            $mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            return new Response('Error al enviar el email', 500);
        }
    }
}