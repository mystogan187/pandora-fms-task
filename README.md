
# PandoraFMS Application

## Prerequisites

- **Docker**: Make sure you have the latest version of Docker and Docker Compose installed on your machine.

## Installation

1. **Clone the Repository**

   ```bash
   git clone https://github.com/mystogan187/pandora-fms-task
   ```

2. **Build and Start the Containers**

   Build Docker images and start the containers by running:

   ```bash
   docker-compose up --build
   ```

   This command will build the images and start the services defined in the `docker-compose.yml` file.

3. **Access the Application**

   Once the containers are running, you can access the web application by navigating to:
   
   - Default will bring you to **/appoinment** 
   ```
   http://localhost:8000
   ```
   Task 1
   ```
    http://localhost:8000/decoding
   ```
   Appointment application (Task 2)
   ```
    http://localhost:8000/appointment
   ```

   Ensure that port `8000` is free and not being used by another application.

## Technologies Used

- **PHP**: Backend programming language.
- **Symfony**: PHP framework for web applications.
- **Doctrine ORM**: Object-relational mapping for database interactions.
- **PHPUnit**: Framework for unit testing in PHP.
- **Docker**: Container platform to deploy the application in isolated environments.
- **MySQL**: Relational database to store the data.
- **Nginx**: Web server to serve the frontend and the API.

## Features

- **Create Appointment**: Add a new appointment to the list.
- **Review Appointment for existing users**: Updates the appointment for another day.
- **Decode PDF**: Show a decoded pdf witch was previously encoded by a mysterious hacker


## Additional Notes

- **Docker Versions**: It is essential to use the latest version of Docker to ensure compatibility and take advantage of the latest features and performance improvements.
- **Port Configuration**: The default port is `8000`. If you need to change it, you can modify the `docker-compose.yml` file.
- **Data Persistence**: Data is stored in Docker volumes to maintain persistence between container restarts.
- **Database Migrations**: If you need to run migrations, you can do so with:

  ```bash
  docker-compose exec php php bin/console doctrine:migrations:migrate
  ```

## Useful Commands

- **Install Dependencies**:

  ```bash
  docker-compose exec php composer install
  ```


---

**Contact**: If you have any questions or need help, you can contact me at [aec.alexandru@gmail.com](mailto:aec.alexandru@gmail.com).

---
