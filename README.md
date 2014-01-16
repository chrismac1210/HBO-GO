Pliki ogólne:
hbogo.php - interfejs dla bok
hbogo_uploader.php - uploader plików

Pliki konfiguracyjne:

admin.php
ftp.php
password.php
hbogo_gui.php

Opis funkcji:

private clear_path - usuwa wszystkie zbędne pliki, zostawia tylko najnowszy
private clear_customer - usuwa abonenta z pliku lub usuwa duplikaty
                        
private file_old - zwraca nazwe najnowszego pliku z danym prefiksem
private file_new - ustala nazwe dla nowo tworzonego pliku
                        
private find_customer - pokazuje w których liniach pliku są informacje o abonencie
private find_files - znajduje wszystkie pliki z danym prefiksem

private password - generuje hasło
private mailto - wysyła stosownego maila
                        
private ftp - upload pliku przez ftp
private sftp - upload pliku przez sftp
                        
public add - aktywuje usługe dla abonenta, wysyla powiadomienie oraz dane potrzebne do rejestracji na maila
public del - deaktywuje usługe dla abonenta, moze wysylac powiadomienie na maila
public re - reaktywuje usługe dla abonenta, moze wyslac powiadomienie na maila
public end - tworzy wlasciwy plik, a nastepnie uploaduje go na ftp.


                                                
                
