# Открываем файл для чтения и считываем все строки
with open('domains.txt', 'r') as file:
    lines = file.readlines()

# Используем множество для хранения уникальных значений
unique_values = set(lines)

# Открываем файл для записи уникальных значений
with open('output.txt', 'w') as file:
    # Записываем уникальные значения обратно в файл
    file.writelines(unique_values)
