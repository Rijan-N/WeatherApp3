<!DOCTYPE html>
<html>
<head>
    <style>
    body {
        background-color: #f8f6ff;
        font-family: Arial, sans-serif;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        background-color: #fff;
    }

    th {
        background-color: #f2f2f2;
        padding: 10px;
        text-align: left;
        font-weight: bold;
    }

    th, td {
        border: 1px solid #ccc;
        padding: 10px;
    }

    tr:nth-child(even) {
        background-color: #f9f9f9;
    }

    tr:hover {
        background-color: #e0e0e0;
    }

    img.weather-icon {
        width: 30px;
        height: 30px;
        vertical-align: middle;
    }

    .title {
        font-size: 24px;
        font-weight: bold;
        margin-bottom: 10px;
        color: #333;
    }

    .message {
        margin-top: 20px;
        font-style: italic;
        color: #666;
    }

    .container {
        max-width: 800px;
        margin: 0 auto;
        padding: 20px;
    }

    .page-header {
        text-align: center;
        margin-bottom: 40px;
    }

    .page-footer {
        text-align: center;
        margin-top: 40px;
        color: #999;
    }
    </style>
</head>
<body>
<?php

date_default_timezone_set('Asia/Kathmandu');

// Create a connection to the MySQL database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "weathers";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set the city for the API request
$city = "South Gloucestershire";

// Get the API key
$apiKey = "9f23b56e8dcad8299bf4e5a2a3fc932b";

// Get the weather data from the API
$url = "http://api.openweathermap.org/data/2.5/weather?q=" . urlencode($city) . "&appid=" . $apiKey;

// Create a new cURL resource
$curl = curl_init();

// Set the cURL options
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

// Execute the cURL request
$response = curl_exec($curl);

// Close the cURL resource
curl_close($curl);

// Check if the API request was successful
if ($response !== false) {
    // Decode the JSON response
    $data = json_decode($response, true);

    // Check if the data contains the required information
    if (isset($data['weather'][0]['description']) && isset($data['main']['temp']) && isset($data['wind']['speed']) && isset($data['main']['humidity'])) {
        // Extract the weather information
        $description = $data['weather'][0]['description'];
        $temperature = $data['main']['temp'];
        $wind = $data['wind']['speed'];
        $humidity = $data['main']['humidity'];
        $icon = $data['weather'][0]['icon'];
        $dt = date('Y-m-d H:i:s');

        // Convert temperature from Kelvin to Celsius
        $temperatureCelsius = round($temperature - 273.15, 2);

        // SQL query to insert the weather data into the table
        $sql = "INSERT INTO `weatherstable` (`description`, `temperature`, `wind`, `city`, `humidity`, `icon`, `dt`) VALUES ('$description', '$temperatureCelsius', '$wind', '$city', '$humidity', '$icon', '$dt')";

        // Execute the query to insert the weather data
        if ($conn->query($sql) === true) {
            echo "Weather data for South Gloucestershire has been stored in the database.";
        } else {
            echo "Error inserting weather data: " . $conn->error;
        }
    } else {
        echo "Unable to retrieve weather data for South Gloucestershire.";
    }
} else {
    echo "API request to OpenWeatherMap failed.";
}

// Delete weather data older than 7 days
$deleteQuery = "DELETE FROM weatherstable WHERE dt < DATE_SUB(NOW(), INTERVAL 7 DAY)";
if ($conn->query($deleteQuery) === TRUE) {
    echo "Weather data older than 7 days has been deleted.";
} else {
    echo "Error deleting weather data: " . $conn->error;
}

// SQL query to fetch data from the table
$sql = "SELECT `description`, `temperature`, `wind`, `city`, `humidity`, `icon`, `dt` FROM `weatherstable`";

// Execute the query and fetch the data
$result = $conn->query($sql);

// Check if any rows were returned
if ($result->num_rows > 0) {
    // Output the table structure
    echo "<table>";
    echo "<tr><th>S.N</th><th>Description</th><th>Temperature (°C)</th><th>Wind</th><th>City</th><th>Humidity</th><th>Icon</th><th>Date and Time</th></tr>";

    // Counter variable for row number
    $counter = 1;

    // Output data of each row
    while ($row = $result->fetch_assoc()) {
        // Access the column values using the column names
        $description = $row["description"];
        $temperatureCelsius = $row["temperature"];
        $wind = $row["wind"];
        $city = $row["city"];
        $humidity = $row["humidity"];
        $icon = $row["icon"];
        $dt = $row["dt"];

        // Display the weather data in a table row
        echo "<tr>";
        echo "<td>$counter</td>";
        echo "<td>$description</td>";
        echo "<td>$temperatureCelsius °C</td>";
        echo "<td>$wind KMPH</td>";
        echo "<td>$city</td>";
        echo "<td>$humidity %</td>";
        echo "<td><img src='http://openweathermap.org/img/wn/$icon.png' alt='Weather Icon'></td>";
        echo "<td>$dt</td>";
        echo "</tr>";

        // Increment the counter
        $counter++;
    }

    // Close the table structure
    echo "</table>";
} else {
    echo "No weather data found.";
}

// Close the database connection
$conn->close();
?>
