import requests

BASE_URL = "http://localhost:8080/api"


def show_tariff(t):
    tv = "є" if t["tv"] else "немає"
    print(f'{t["id"]}. {t["name"]} - {t["speed_mbps"]} Мбіт/с, '
          f'{t["price_uah"]} грн/міс, ТБ: {tv}')


def get_all():
    response = requests.get(f"{BASE_URL}/tariffs")
    for tariff in response.json():
        show_tariff(tariff)


def get_one():
    tariff_id = input("Введіть id тарифу: ")
    response = requests.get(f"{BASE_URL}/tariffs/{tariff_id}")
    if response.status_code == 200:
        show_tariff(response.json())
    else:
        print(response.json()["error"])


def main():
    while True:
        print("\n1 - Усі тарифи\n2 - Тариф за id\n0 - Вихід")
        choice = input("Ваш вибір: ")
        try:
            if choice == "1":
                get_all()
            elif choice == "2":
                get_one()
            elif choice == "0":
                break
            else:
                print("Невірний вибір")
        except requests.exceptions.ConnectionError:
            print("Сервер недоступний. Запустіть API.")


main()