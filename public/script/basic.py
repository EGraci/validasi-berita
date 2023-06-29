# analyse_string.py
#!/usr/bin/env python
#!/usr/bin/python

import json
import numpy as np
import sys
import mysql.connector

db = mysql.connector.connect(
  host="localhost",
  user="root",
  password="",
  database="hoax"
)
conn = mycursor = db.cursor()
conn.execute("SELECT * FROM `hoax`");
dataset = conn.fetchall()
hoax = []
i = 1

for row in dataset:
    hoax.append = i
    i += 1
    # print(json.loads(row[1]))
    # print("\n")

print(hoax)

conn.close()

# print(dataset)

n = len(sys.argv)
berita = sys.argv[1];
# dataset = np.array(json.loads(["beredar","media","sosial","sebuah","artikel","menyebutkan","presiden","joko","widodo","maju","pilpres","2024","artikel","tersebut","disertai","judul","jokowi","maju","pilpres","2024","rakyat","sangat","bahagia","rakyat","ri","menyayanginya"]))
# print(numpy)