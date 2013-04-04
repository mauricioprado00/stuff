export PASSPHRASE=SomeLongGeneratedHardToCrackKey
export FTP_PASSWORD=txrqvvob
duplicity /var/www/archivos/ ftp://baixavisio@www.baixavisio.com/backups/files/baixa
unset PASSPHRASE
unset FTP_PASSWORD
