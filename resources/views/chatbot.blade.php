<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Travel Assistant Chatbot</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .chat-container {
            max-width: 800px;
            margin: 20px auto;
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
        }
        .chat-messages {
            height: 400px;
            overflow-y: auto;
            padding: 20px;
            background: #f9f9f9;
        }
        .message {
            margin-bottom: 15px;
            padding: 10px;
            border-radius: 8px;
            max-width: 80%;
        }
        .user-message {
            background: #007bff;
            color: white;
            margin-left: auto;
        }
        .bot-message {
            background: #e9ecef;
            color: black;
        }
        .chat-input {
            padding: 20px;
            background: white;
            border-top: 1px solid #ddd;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="chat-container">
            <div class="chat-messages" id="chatMessages">
                <div class="message bot-message">
                    Привет! Я ваш туристический помощник. Я могу помочь вам с информацией о погоде, отелях и достопримечательностях. С чем могу помочь?
                </div>
            </div>
            <div class="chat-input">
                <div class="input-group">
                    <input type="text" class="form-control" id="userInput" placeholder="Введите ваш запрос...">
                    <button class="btn btn-primary" onclick="sendMessage()">Отправить</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        async function sendMessage() {
            const userInput = document.getElementById('userInput');
            const chatMessages = document.getElementById('chatMessages');
            const message = userInput.value.trim();
            
            if (!message) return;

            // Добавляем сообщение пользователя
            chatMessages.innerHTML += `
                <div class="message user-message">
                    ${message}
                </div>
            `;
            
            // Очищаем поле ввода
            userInput.value = '';

            try {
                let endpoint = '';
                let body = {};

                // Определяем тип запроса
                if (message.toLowerCase().includes('погода')) {
                    endpoint = '/api/weather';
                    const country = prompt('Пожалуйста, введите страну:');
                    const month = prompt('Пожалуйста, введите месяц:');
                    body = { country, month };
                } else if (message.toLowerCase().includes('отель')) {
                    endpoint = '/api/hotels';
                    const country = prompt('Пожалуйста, введите страну:');
                    const city = prompt('Пожалуйста, введите город:');
                    body = { country, city };
                } else if (message.toLowerCase().includes('достопримечательность')) {
                    endpoint = '/api/attractions';
                    const country = prompt('Пожалуйста, введите страну:');
                    body = { country };
                }

                if (endpoint) {
                    const response = await fetch(endpoint, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(body)
                    });

                    const data = await response.json();
                    
                    if (response.ok) {
                        // Добавляем ответ бота
                        chatMessages.innerHTML += `
                            <div class="message bot-message">
                                ${data.message}
                            </div>
                        `;
                    } else {
                        chatMessages.innerHTML += `
                            <div class="message bot-message">
                                ${data.error || 'Произошла ошибка'}
                            </div>
                        `;
                    }
                } else {
                    chatMessages.innerHTML += `
                        <div class="message bot-message">
                            Извините, я не понял ваш запрос. Вы можете спросить о погоде, отелях или достопримечательностях.
                        </div>
                    `;
                }
            } catch (error) {
                console.error('Error:', error);
                chatMessages.innerHTML += `
                    <div class="message bot-message">
                        Произошла ошибка при обработке вашего запроса. Пожалуйста, попробуйте еще раз.
                    </div>
                `;
            }

            // Прокручиваем чат вниз
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }

        // Добавляем обработчик нажатия Enter
        document.getElementById('userInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                sendMessage();
            }
        });
    </script>
</body>
</html> 