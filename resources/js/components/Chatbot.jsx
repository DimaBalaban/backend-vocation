import React, { useState } from 'react';
import axios from 'axios';

const Chatbot = () => {
    const [country, setCountry] = useState('');
    const [month, setMonth] = useState('');
    const [city, setCity] = useState('');
    const [weatherResult, setWeatherResult] = useState(null);
    const [hotelsResult, setHotelsResult] = useState(null);
    const [attractionsResult, setAttractionsResult] = useState(null);
    const [error, setError] = useState(null);

    const handleWeatherSubmit = async (e) => {
        e.preventDefault();
        try {
            const response = await axios.post('/api/weather', {
                country,
                month
            });
            setWeatherResult(response.data);
            setError(null);
        } catch (err) {
            setError(err.response?.data?.message || 'Произошла ошибка при получении данных о погоде');
        }
    };

    const handleHotelsSubmit = async (e) => {
        e.preventDefault();
        try {
            const response = await axios.post('/api/hotels', {
                country,
                city
            });
            setHotelsResult(response.data);
            setError(null);
        } catch (err) {
            setError(err.response?.data?.message || 'Произошла ошибка при поиске отелей');
        }
    };

    const handleAttractionsSubmit = async (e) => {
        e.preventDefault();
        try {
            const response = await axios.post('/api/attractions', {
                country
            });
            setAttractionsResult(response.data);
            setError(null);
        } catch (err) {
            setError(err.response?.data?.message || 'Произошла ошибка при поиске достопримечательностей');
        }
    };

    return (
        <div className="container mt-4">
            {error && <div className="alert alert-danger">{error}</div>}

            <div className="row">
                <div className="col-md-4">
                    <div className="card mb-4">
                        <div className="card-header">
                            <h5>Узнать погоду</h5>
                        </div>
                        <div className="card-body">
                            <form onSubmit={handleWeatherSubmit}>
                                <div className="mb-3">
                                    <label className="form-label">Страна</label>
                                    <input
                                        type="text"
                                        className="form-control"
                                        value={country}
                                        onChange={(e) => setCountry(e.target.value)}
                                        required
                                    />
                                </div>
                                <div className="mb-3">
                                    <label className="form-label">Месяц</label>
                                    <select
                                        className="form-control"
                                        value={month}
                                        onChange={(e) => setMonth(e.target.value)}
                                        required
                                    >
                                        <option value="">Выберите месяц</option>
                                        <option value="January">Январь</option>
                                        <option value="February">Февраль</option>
                                        <option value="March">Март</option>
                                        <option value="April">Апрель</option>
                                        <option value="May">Май</option>
                                        <option value="June">Июнь</option>
                                        <option value="July">Июль</option>
                                        <option value="August">Август</option>
                                        <option value="September">Сентябрь</option>
                                        <option value="October">Октябрь</option>
                                        <option value="November">Ноябрь</option>
                                        <option value="December">Декабрь</option>
                                    </select>
                                </div>
                                <button type="submit" className="btn btn-primary">Узнать погоду</button>
                            </form>
                            {weatherResult && (
                                <div className="mt-3">
                                    <p>{weatherResult.message}</p>
                                    <a href={weatherResult.source} target="_blank" rel="noopener noreferrer">
                                        Подробнее
                                    </a>
                                </div>
                            )}
                        </div>
                    </div>
                </div>

                <div className="col-md-4">
                    <div className="card mb-4">
                        <div className="card-header">
                            <h5>Поиск отелей</h5>
                        </div>
                        <div className="card-body">
                            <form onSubmit={handleHotelsSubmit}>
                                <div className="mb-3">
                                    <label className="form-label">Страна</label>
                                    <input
                                        type="text"
                                        className="form-control"
                                        value={country}
                                        onChange={(e) => setCountry(e.target.value)}
                                        required
                                    />
                                </div>
                                <div className="mb-3">
                                    <label className="form-label">Город</label>
                                    <input
                                        type="text"
                                        className="form-control"
                                        value={city}
                                        onChange={(e) => setCity(e.target.value)}
                                        required
                                    />
                                </div>
                                <button type="submit" className="btn btn-primary">Найти отели</button>
                            </form>
                            {hotelsResult && (
                                <div className="mt-3">
                                    <p>{hotelsResult.message}</p>
                                    <a href={hotelsResult.search_url} target="_blank" rel="noopener noreferrer">
                                        Посмотреть отели
                                    </a>
                                </div>
                            )}
                        </div>
                    </div>
                </div>

                <div className="col-md-4">
                    <div className="card mb-4">
                        <div className="card-header">
                            <h5>Достопримечательности</h5>
                        </div>
                        <div className="card-body">
                            <form onSubmit={handleAttractionsSubmit}>
                                <div className="mb-3">
                                    <label className="form-label">Страна</label>
                                    <input
                                        type="text"
                                        className="form-control"
                                        value={country}
                                        onChange={(e) => setCountry(e.target.value)}
                                        required
                                    />
                                </div>
                                <button type="submit" className="btn btn-primary">Найти достопримечательности</button>
                            </form>
                            {attractionsResult && (
                                <div className="mt-3">
                                    <p>{attractionsResult.message}</p>
                                    <a href={attractionsResult.source} target="_blank" rel="noopener noreferrer">
                                        Подробнее
                                    </a>
                                </div>
                            )}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
};

export default Chatbot; 