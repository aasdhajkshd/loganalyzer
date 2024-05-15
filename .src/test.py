import datetime
import time

def convert_date():
    while True:
        date_string = input("Укажите дату вида '2023-06-14 12:34:56': ")
        try:
            date_time = datetime.datetime.strptime(date_string, "%Y-%m-%d %H:%M:%S")
            unix_time = int(time.mktime(date_time.timetuple()))
        except ValueError:
            print("Ошибка преобразования")
        else:     
            print(unix_time)
            break

convert_date()