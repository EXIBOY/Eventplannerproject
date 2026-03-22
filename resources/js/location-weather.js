const GEOLOCATION_OPTIONS = {
    enableHighAccuracy: true,
    timeout: 10000,
    maximumAge: 300000,
};

const WEATHER_CODES = {
    0: 'Clear sky',
    1: 'Mainly clear',
    2: 'Partly cloudy',
    3: 'Overcast',
    45: 'Fog',
    48: 'Rime fog',
    51: 'Light drizzle',
    53: 'Moderate drizzle',
    55: 'Dense drizzle',
    56: 'Freezing drizzle',
    57: 'Heavy freezing drizzle',
    61: 'Light rain',
    63: 'Moderate rain',
    65: 'Heavy rain',
    66: 'Light freezing rain',
    67: 'Heavy freezing rain',
    71: 'Light snow',
    73: 'Moderate snow',
    75: 'Heavy snow',
    77: 'Snow grains',
    80: 'Rain showers',
    81: 'Heavy rain showers',
    82: 'Violent rain showers',
    85: 'Snow showers',
    86: 'Heavy snow showers',
    95: 'Thunderstorm',
    96: 'Thunderstorm with hail',
    99: 'Severe thunderstorm with hail',
};

const STATUS_TONES = {
    neutral: ['text-slate-500'],
    pending: ['text-amber-700'],
    success: ['text-emerald-700'],
    error: ['text-rose-700'],
};

function whenReady(callback) {
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', callback, { once: true });
        return;
    }

    callback();
}

function query(root, selector) {
    return root.querySelector(selector);
}

function setStatus(element, message, tone = 'neutral') {
    if (!element) {
        return;
    }

    Object.values(STATUS_TONES).flat().forEach((className) => {
        element.classList.remove(className);
    });

    element.textContent = message;
    element.dataset.tone = tone;
    element.classList.add(...(STATUS_TONES[tone] ?? STATUS_TONES.neutral));
}

function formatCoordinate(value) {
    return Number(value).toFixed(4);
}

function formatTemperature(value) {
    return value == null ? '--' : `${Math.round(value)}°C`;
}

function formatWind(value) {
    return value == null ? '--' : `${Math.round(value)} km/h`;
}

function formatPrecipitation(value) {
    return value == null ? '--' : `${Number(value).toFixed(1)} mm`;
}

function weatherLabel(code, isDay) {
    if (code === 0 && isDay === 0) {
        return 'Clear night';
    }

    return WEATHER_CODES[code] ?? 'Current conditions';
}

function geolocationErrorMessage(error) {
    switch (error?.code) {
        case 1:
            return 'Location permission was denied. Allow access and try again.';
        case 2:
            return 'Your device location is unavailable right now.';
        case 3:
            return 'Location lookup timed out. Try again in a stronger signal area.';
        default:
            return 'Unable to access your device location.';
    }
}

function requestDeviceLocation() {
    return new Promise((resolve, reject) => {
        if (!('geolocation' in navigator)) {
            reject(new Error('This browser does not support device location.'));
            return;
        }

        navigator.geolocation.getCurrentPosition(resolve, reject, GEOLOCATION_OPTIONS);
    });
}

async function fetchWeather(latitude, longitude) {
    const url = new URL('https://api.open-meteo.com/v1/forecast');

    url.search = new URLSearchParams({
        latitude: String(latitude),
        longitude: String(longitude),
        current: 'temperature_2m,apparent_temperature,precipitation,weather_code,wind_speed_10m,is_day',
        daily: 'temperature_2m_max,temperature_2m_min',
        forecast_days: '1',
        timezone: 'auto',
    }).toString();

    const response = await fetch(url.toString(), {
        headers: {
            Accept: 'application/json',
        },
    });

    if (!response.ok) {
        throw new Error('The weather service could not be reached.');
    }

    return response.json();
}

function initDeviceLocationInputs() {
    document.querySelectorAll('[data-device-location]').forEach((container) => {
        const input = query(container, '[data-location-input]');
        const button = query(container, '[data-location-trigger]');
        const status = query(container, '[data-location-status]');

        if (!input || !button || !status) {
            return;
        }

        if (!('geolocation' in navigator)) {
            button.disabled = true;
            setStatus(status, 'Device location is not supported in this browser.', 'error');
            return;
        }

        button.addEventListener('click', async () => {
            button.disabled = true;
            setStatus(status, 'Checking your device location…', 'pending');

            try {
                const position = await requestDeviceLocation();
                const { latitude, longitude, accuracy } = position.coords;

                input.value = `Current device location (${formatCoordinate(latitude)}, ${formatCoordinate(longitude)})`;
                input.dispatchEvent(new Event('input', { bubbles: true }));
                input.dispatchEvent(new Event('change', { bubbles: true }));

                setStatus(
                    status,
                    `Inserted your current coordinates with about ${Math.round(accuracy)}m accuracy. Replace this with a venue name if needed.`,
                    'success',
                );
            } catch (error) {
                setStatus(status, geolocationErrorMessage(error), 'error');
            } finally {
                button.disabled = false;
            }
        });
    });
}

function updateWeatherField(widget, selector, value) {
    const element = query(widget, selector);

    if (element) {
        element.textContent = value;
    }
}

function initWeatherWidgets() {
    document.querySelectorAll('[data-weather-widget]').forEach((widget) => {
        const button = query(widget, '[data-weather-trigger]');
        const status = query(widget, '[data-weather-status]');

        if (!button || !status) {
            return;
        }

        const requiresSecureContext = !window.isSecureContext &&
            !['localhost', '127.0.0.1'].includes(window.location.hostname);

        if (requiresSecureContext) {
            setStatus(status, 'Device location usually requires HTTPS or localhost in the browser.', 'pending');
        }

        if (!('geolocation' in navigator)) {
            button.disabled = true;
            setStatus(status, 'Device location is not supported in this browser.', 'error');
            return;
        }

        button.addEventListener('click', async () => {
            button.disabled = true;
            setStatus(status, 'Checking device location and loading weather…', 'pending');

            try {
                const position = await requestDeviceLocation();
                const { latitude, longitude } = position.coords;
                const weather = await fetchWeather(latitude, longitude);
                const current = weather.current ?? {};
                const daily = weather.daily ?? {};

                updateWeatherField(
                    widget,
                    '[data-weather-location]',
                    `Near ${formatCoordinate(latitude)}, ${formatCoordinate(longitude)}`,
                );
                updateWeatherField(widget, '[data-weather-temperature]', formatTemperature(current.temperature_2m));
                updateWeatherField(
                    widget,
                    '[data-weather-description]',
                    weatherLabel(current.weather_code, current.is_day),
                );
                updateWeatherField(
                    widget,
                    '[data-weather-summary]',
                    `High ${formatTemperature(daily.temperature_2m_max?.[0])} · Low ${formatTemperature(daily.temperature_2m_min?.[0])}`,
                );
                updateWeatherField(widget, '[data-weather-feels-like]', formatTemperature(current.apparent_temperature));
                updateWeatherField(widget, '[data-weather-precipitation]', formatPrecipitation(current.precipitation));
                updateWeatherField(widget, '[data-weather-wind]', formatWind(current.wind_speed_10m));

                setStatus(status, 'Local weather loaded from your current device location.', 'success');
            } catch (error) {
                const message = error instanceof Error
                    ? error.message
                    : geolocationErrorMessage(error);

                setStatus(status, message, 'error');
            } finally {
                button.disabled = false;
            }
        });
    });
}

whenReady(() => {
    initDeviceLocationInputs();
    initWeatherWidgets();
});
