SELECT tanggal, id_bank_tujuan
FROM bank_masuk
WHERE YEAR(tanggal)=2025
ORDER BY tanggal ASC;