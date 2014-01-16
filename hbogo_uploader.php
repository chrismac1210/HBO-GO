<?php require 'hbogo_gui.php'?>

<?php

class Generator
{
	
	private function file_old($prefix) 
	{
		/*	Zwraca nazwe najnowszego pliku z danym prefiksem	*/
		return $this->clear_path($prefix, true);	
	}
	
	private function file_new($prefix)
	{
		/*	Ustala nazwe dla najnowszego pliku z danym prefiksem	*/
		return $prefix.'_'.date('Ymd').'_'.date('His').'.txt';
	}
	
	private function clear_path($prefix, $return_status)
	{
		/*	Zostawia tylko najnowszy plik z wybranym prefiksem. W przypadku gdy return_status jest true, zwraca nazwe tego pliku	*/
		$lista_plikow = $this->find_files($prefix);
		rsort($lista_plikow);
		
		for ($i = 1; $i < sizeof($lista_plikow); $i++)
		{
		
			unlink($lista_plikow[$i]);
		
		}
		
		if ($return_status == true)
		{	
			
			return $lista_plikow[0];
			
		}
		
	}
	
	private function clear_customer($plik, $id, $all)
	{
		/*	Usuwa abonentów (lub duplikaty )z pliku i tworzy nowy plik	*/
		$lista_abonentow = $this->find_customer($plik, $id);
		$plik_tablica = file($plik);
		$return_array = array();
		
		$file_new = $this->file_new('PRODTKC');
		
		$fileHandle = fopen($file_new, 'w+') or die ("Nie mozna utworzyć pliku!");
		$return_array[] = $file_new;
		
		if (sizeof($lista_abonentow) == 0)
		{
			
			$return_array[] = true;
			
		}
		else if (sizeof($lista_abonentow) <= 1)
		{
		
			$return_array[] = false;
		}
		else
		{
		
			$return_array[] = true;
		
		}
		
		if ($all == true)
		{
			
			$j = 0;
			
		}
		else if ($all == false)
		{
			
			$j= 1;
			
		}
		
		for ($i = $j; $i < sizeof($lista_abonentow); $i++)
		{
			
			unset($plik_tablica[$lista_abonentow[$i]]);
			
		}
		
		foreach($plik_tablica as $line) 
		{ 
		
			fwrite($fileHandle,$line); 
		}
		
		fclose($fileHandle);
		return $return_array;
	}
	
	private function find_files($prefix) 
	{	
		/* Zwraca tablice wszystkich znalezionych plików */
		$lista = array();
		
		foreach (glob($prefix."_"."*.txt") as $plik) 
		{
			
			$lista[] = $plik;
		}
		return $lista;
	}
	
	private function find_customer($plik, $id)
	{
		/*	Zwraca numery linii w danym pliku, gdzie są informacje o abonencie 	*/
		$i = 0;
		$customers = array();
		
		$fileHandle = fopen($plik, "r");
		while (!feof($fileHandle)) 
		{

			$customer = fgets($fileHandle);
			
			if ($id[0] == $customer[0] and $id[1] == $customer[1] and $id[2] == $customer[2] and $id[3] == $customer[3] and $id[4] == $customer[4] and $id[5] == $customer[5])
			{
				$customers[] = $i;
			}
			$i++;
		}
		
		fclose($fileHandle);		
		return $customers;
	}
	
	private function mailto($id, $haslo, $mail, $akcja)
	{	
		/*	Wysyła stosownego maila		*/

		if ($akcja == 'ftp')
		{
		
			$tytul = "Upload pliku HBO GO";
			$wiadomosc = "Pomyślnie wrzucono na FTP plik: " .$id;
		
		}
		
		$naglowek = 'From: no-reply@tkchopin.pl' . "\r\n" .
					'Reply-To: ibok@tkchopin.pl' . "\r\n" .
					'X-Mailer: PHP/' . phpversion();
		
		imap_mail($mail, $tytul, $wiadomosc, $naglowek);
			
	}
	
	
	private function ftp($plik)
	{
		/* Wrzuca odpowiedni plik na FTP */
		require 'ftp.php';

		echo "Próba otworzenia pliku do uploadu... \n";
		
		$fileHandle = fopen($plik, 'r') or die ("Nie można otworzyć pliku do uploadu");
		
		echo "Sukces!\n";
		echo "Próba połączenia z serwerem FTP... \n";
	
		$connect = ftp_connect($server) or die ("Nie można się połaczyć z FTP");
		echo "Sukces!\n";
		ftp_login($connect, $user, $pass);
		echo "Próbuje uploadu pliku na serwer... \n";
		ftp_fput($connect, $plik, $fileHandle, FTP_BINARY) or die("Upload pliku nie udany!");
		ftp_close($connect);
		fclose($fileHandle);
		echo "Upload pliku zakończony pomyślnie! \n";
	}
	
	public function end()
	{
		require 'admin.php';
		
		$file_old = $this->file_old('PRODTKC');
		$file_new = $this->file_new('TKC');
		
		echo "Uzywany plik produkcyjny: ".$file_old."\n" ;
		
		$fileHandle = fopen($file_new, 'a+') or die ('Nie mozna utworzyc pliku produkcyjnego');
		copy($file_old, $file_new) or die ('Nie mozna skopiowac zawartosci do pliku produkcyjnego!');
		fclose($fileHandle);
		
		echo "Utworzono plik produkcyjny: ".$file_new."\n" ;
		
		$this->ftp($file_new);
		$this->mailto($file_new, Null, $admin, 'ftp');
	
	}	
	
} 

function ssh($polecenie)
{
	$hbogo = new Generator;
	
	switch ($polecenie)
	{
		case 'end':
			$hbogo->end();
			break;
		
	}
}

ssh($argv[1]);	
?>
