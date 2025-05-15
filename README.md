<h1 align="center" id="title">Phone Number Generator and Validator</h1>

<p id="description">This project is a PHP-based application that generates random phone numbers for selected African countries validates them using a Lumen microservice and stores data in MongoDB. It features a front-end web interface a RESTful microservice a MongoDB database and a Mongo Express GUI for database management all containerized with Docker.</p>



<h2>🧐 Features</h2>

Here're some of the project's best features:

*   Generate random phone numbers for: - South Africa (+27) - Nigeria (+234) - Kenya (+254) - Zimbabwe (+263) - Morocco (+212)
*   Validate numbers using \`libphonenumber-for-php\`.
*   Store generated numbers and validation results in MongoDB.
*   Display results in a styled table
*   Access MongoDB via Mongo Express GUI at \[http://localhost:8081\]
*   Unit tests for the microservice using PHPUnit.

<h2>🗂️ Project Structure:</h2>
php_assessment/ <br>
├── frontend/ <br>
│ ├── index.php <br>
│ ├── composer.json <br>
│ └── tests/ <br>
├── microservice/ <br>
│ ├── Dockerfile <br>
│ ├── app/Http/Controllers/ValidatorController.php <br>
│ ├── composer.json <br>
│ ├── artisan <br>
│ ├── public/index.php <br>
│ ├── routes/web.php <br>
│ └── tests/ValidatorTest.php <br>
├── docker-compose.yml <br>
├── Dockerfile <br>
└── README.md <br>

<h2>🛠️ Installation Steps:</h2>

<p>1. Clone the Repository</p>

```
git clone https://github.com/your-username/php_assessment.git  cd php_assessment
```


<p>2. Install Frontend Dependencies</p>

```
cd frontend composer install cd ..
```

<p>3. Install Microservice Dependencies</p>

```
cd microservice composer install cd ..
```

<p>4. Run Docker Containers</p>

```
docker-compose up --build
```
<h2>🌐 Access the Application</h2>

Frontend: http://localhost:8080

Mongo Express: http://localhost:8081 <br>
Username: admin
Password: password

Microservice API: http://localhost:9000/api/validate

<h2>🧪 Usage</h2>

Open http://localhost:8080

Select quantity (1–100) and country code

Click Generate

View results in a table with summary stats like:

"Out of 5 numbers generated, 3 were valid. That’s 60.00% valid."

<h2>🧑‍💻 Author</h2>
Judel — PHP Developer <br>
📍 South Africa