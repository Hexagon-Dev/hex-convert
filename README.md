# HexConvert
HexConvert - Приложение для конвертации картинок в другие размеры.  
Перед началом установки убедитесь что у вас установлены все зависимости:
```bash
# загрузка списков пакетов из репозиториев
sudo apt-get update

# установка докера
sudo apt-get install docker-ce docker-ce-cli containerd.io

# установка приложения автоматизированной сборки
sudo apt-get install make
```
Быстрый запуск:
```bash
# клонируем репозиторий
git clone https://github.com/hexagon14/hexconvert.git

# меняем директорию
cd hexconvert

# устанавливаем
make install-dev

# запускаем
make up

# мигрируем таблицы в базу данных
make migrate
```
Перейдите на http://localhost  
Если вы видите надпись Hex-Convert - значит приложение успешно установлено.
### Действия:
* Загрузка изображения - POST: http://localhost/api/tasks
* Получение статуса - GET: http://localhost/api/your-image-uuid-here
* Загрузка готового изображения - GET: https://localhost/api/your-image-path-here
