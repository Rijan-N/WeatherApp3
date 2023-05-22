const weatherApi = {
    key: '9f23b56e8dcad8299bf4e5a2a3fc932b',
    baseUrl: 'https://api.openweathermap.org/data/2.5/weather',
  };
  
  if ('serviceWorker' in navigator) {
    navigator.serviceWorker.addEventListener('message', function(event) {
      if (event.data && event.data.command === 'CACHE_SIZE_RESULT') {
        console.log('Cache size:', event.data.size + 'MB');
      }
    });
  }
  
  let searchInputBox = document.getElementById('input-box');
  searchInputBox.value = 'South Gloucestershire';
  showSavedWeatherData(searchInputBox.value);
  
  searchInputBox.addEventListener('keypress', function (event) {
    if (event.keyCode === 13) {
      updateWeather(searchInputBox.value);
    }
  });
  
  function saveWeatherData(city, weatherData) {
    const storedWeatherData = getWeatherDataFromLocalStorage();
    storedWeatherData[city] = {
      data: weatherData,
      timestamp: new Date().getTime(),
    };
  
    localStorage.setItem('weatherData', JSON.stringify(storedWeatherData));
  }
  
  function getWeatherDataFromLocalStorage() {
    const weatherData = localStorage.getItem('weatherData');
    if (weatherData) {
      return JSON.parse(weatherData);
    }
    return {};
  }
  
  function updateWeather(city) {
    return getWeatherReport(city)
      .then(function (weatherData) {
        saveWeatherData(city, weatherData);
        showWeatherReport(weatherData);
      })
      .catch(function (error) {
        console.error('Error fetching weather data:', error);
        showErrorMessage('Failed to fetch weather data. Please check your internet connection.');
      });
  }
  
  function showErrorMessage(message) {
    let errorContainer = document.getElementById('weather-body');
    errorContainer.innerHTML = `<div class="error-message">${message}</div>`;
  }
  
  function showSavedWeatherData(city) {
    const weatherData = getWeatherDataFromLocalStorage();
    if (weatherData && weatherData[city]) {
      const { data, timestamp } = weatherData[city];
      const currentDate = new Date();
      const hoursDifference = Math.floor((currentDate - new Date(timestamp)) / (1000 * 60 * 60));
  
      if (hoursDifference <= 1) {
        showWeatherReport(data);
      } else {
        updateWeather(city);
      }
    } else {
      updateWeather(city);
    }
  }
  
  function getWeatherReport(city) {
    return fetch(`${weatherApi.baseUrl}?q=${city}&appid=${weatherApi.key}&units=metric`)
      .then(function (response) {
        return response.json();
      });
  }
  
  function showWeatherReport(weather) {
    let cityCode = weather.cod;
    if (cityCode === '400') {
      swal('Empty Input', 'Please enter a city', 'error');
      reset();
    } else if (cityCode === '404') {
      swal('Bad Input', 'Entered city not found', 'warning');
      reset();
    } else {
      let op = document.getElementById('weather-body');
      op.style.display = 'block';
      let todayDate = new Date();
      let parent = document.getElementById('parent');
      let weather_body = document.getElementById('weather-body');
      weather_body.innerHTML = `
        <div class="location-deatils">
          <div class="city" id="city">${weather.name}, ${weather.sys.country}</div>
          <div class="date" id="date">${dateManage(todayDate)}</div>
        </div>
        <div class="weather-status">
          <div class="temp" id="temp">${Math.round(weather.main.temp)}&deg;C </div>
          <div class="weather" id="weather">${weather.weather[0].main} <i class="${getIconClass(weather.weather[0].main)}"></i></div>
          <div class="min-max" id="min-max">${Math.floor(weather.main.temp_min)}&deg;C (min) / ${Math.ceil(weather.main.temp_max)}&deg;C (max) </div>
          <div class="extra-info">
            <div class="info-item"><i class="fas fa-wind"></i> Wind - ${weather.wind.speed} km/h</div>
            <div class="info-item"><i class="fas fa-tint"></i> Humidity - ${weather.main.humidity}%</div>
            <div class="info-item"><i class="fas fa-tachometer-alt"></i> Pressure - ${weather.main.pressure} hPa</div>
          </div>
          <div id="updated_on">Updated as of ${getTime(todayDate)}</div>
        </div>
      `;
  
      if (parent.classList.contains('parent')) {
        parent.classList.remove('parent');
        parent.classList.add('bg-image');
      }
  
      if (op.classList.contains('loader')) {
        op.classList.remove('loader');
        op.classList.add('weather-box');
      }
    }
  }
  
  function dateManage(dateArg) {
    let days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
  
    let months = [
      'January',
      'February',
      'March',
      'April',
      'May',
      'June',
      'July',
      'August',
      'September',
      'October',
      'November',
      'December',
    ];
  
    let year = dateArg.getFullYear();
    let month = months[dateArg.getMonth()];
    let date = dateArg.getDate();
    let day = days[dateArg.getDay()];
  
    return `${date} ${month} (${day}), ${year}`;
  }
  
  function getTime(dateArg) {
    let hours = dateArg.getHours();
    let minutes = dateArg.getMinutes();
    let ampm = hours >= 12 ? 'PM' : 'AM';
  
    hours = hours % 12;
    hours = hours ? hours : 12;
    minutes = minutes < 10 ? '0' + minutes : minutes;
  
    return `${hours}:${minutes} ${ampm}`;
  }
  
  function getIconClass(weather) {
    let weatherIcon = '';
    switch (weather) {
      case 'Clear':
        weatherIcon = 'fas fa-sun';
        break;
      case 'Clouds':
        weatherIcon = 'fas fa-cloud';
        break;
      case 'Rain':
        weatherIcon = 'fas fa-cloud-rain';
        break;
      case 'Drizzle':
        weatherIcon = 'fas fa-cloud-showers-heavy';
        break;
      case 'Thunderstorm':
        weatherIcon = 'fas fa-bolt';
        break;
      case 'Snow':
        weatherIcon = 'fas fa-snowflake';
        break;
      default:
        weatherIcon = 'fas fa-cloud';
        break;
    }
    return weatherIcon;
  }
  
  function getBackgroundImage(weatherCondition) {
    let backgroundImage = '';
  
    switch (weatherCondition) {
      case 'clear':
        backgroundImage = 'url(img/RijanNeupane_2330765clear.jpg)';
        break;
      case 'clouds':
        backgroundImage = 'url(img/RijanNeupane_2330765clouds.jpg)';
        break;
      case 'rain':
        backgroundImage = 'url(img/RijanNeupane_2330765rainy.jpg)';
        break;
      case 'drizzle':
        backgroundImage = 'url(img/RijanNeupane_2330765drizzle.jpg)';
        break;
      case 'thunderstorm':
        backgroundImage = 'url(img/RijanNeupane_2330765thunderstorm.jpg)';
        break;
      case 'snow':
        backgroundImage = 'url(img/RijanNeupane_2330765snow.jpg)';
        break;
      default:
        backgroundImage = 'url(img/RijanNeupane_2330765bg.jpg)';
        break;
    }
  
    return backgroundImage;
  }
  
  function reset() {
    let input = document.getElementById('input-box');
    input.value = '';
  }
  
  function addZero(i) {
    if (i < 10) {
      i = '0' + i;
    }
    return i;
  }
  