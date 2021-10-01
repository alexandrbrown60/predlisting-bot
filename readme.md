### Telegram bot для предлистинга объектов недвижимости

# Для чего нужен этот бот?
Данный бот предназначен для агентств недвижимости, в частности для "бронирования" объекта за агентом

# Как работает бот?
В бот необходимо вписать адрес, далее происходит проверка объекта в CRM системе (в данном случае, в Intrum CRM), а затем проверка в локальной базе данных MySQL. 
Если объект присутствует в одной из баз данных, пользователь получить уведомление. Если объект недвижимости свободен, он забронируется за пользователем на 2 дня вперед. 