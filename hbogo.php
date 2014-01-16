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
	
	private function password($id)
	{
		/*	Tworzy haslo na podstawie ID abonenta	*/
		require 'password.php';
				
		for ($i=0;$i<12;$i++)
		{
				
			if ($i%4==0 and $i!=0)
			{	
			
				$haslo .= "-";
			
			}
			if ($i==0)
			{
			
				$haslo = $hashOutput[$i];
			
			}
			else
			{
			
				$haslo .= $hashOutput[$i];
			
			}
			
		}
		
		return $haslo;
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
	
	private function mailto($id, $haslo, $mail, $akcja, $mail_pomocniczy)
	{	
		/*	Wysyła stosownego maila		*/
		if ($akcja == 'add')
		{
			
			$tytul = "Aktywacja usługi HBO GO";
			$wiadomosc = "Twoje konto zostanie wkrótce aktywowane! \r\n";
			$wiadomosc.= "Dane potrzebne do rejestracji na stronie http://www.hbogo.pl" .":\r\n";
			$wiadomosc.= "Numer ewidencyjny: ". $id. "\r\n";
			$wiadomosc.= "Hasło: ". $haslo. "\r\n\r\n";
			$wiadomosc.= "Wiadomość została wygenerowana automatycznie, prosimy na nią nie odpowiadać.";
			$wiadomosc.= "W razie problemów prosimy o kontakt z Biurem Obsługi Klienta (telefon: 738 97 01 lub e-mail: ibok@tkchopin.pl)";
		
		}
		else if ($akcja == 'del')
		{
		
			$tytul = "Deaktywacja usługi HBO GO";
			$wiadomosc = "Twoje konto zostanie wkrótce deaktywowane! \r\n \r\n";
			$wiadomosc.= "Wiadomość została wygenerowana automatycznie, prosimy na nią nie odpowiadać.";
			$wiadomosc.= "W razie problemów prosimy o kontakt z Biurem Obsługi Klienta (telefon: 738 97 01 lub e-mail: ibok@tkchopin.pl)";
		
		}
		else if ($akcja == 're')
		{
		
			$tytul = "Reaktywacja usługi HBO GO";
			$wiadomosc = "Twoje konto zostanie wkrótce reaktywowane! \r\n \r\n";
			$wiadomosc.= "Wiadomość została wygenerowana automatycznie, prosimy na nią nie odpowiadać.";
			$wiadomosc.= "W razie problemów prosimy o kontakt z Biurem Obsługi Klienta (telefon: 738 97 01 lub e-mail: ibok@tkchopin.pl)";
		
		}

		else if ($akcja == 'check_add')
		{
		
			$tytul = "Aktywacja HBO GO - ID: " .$id;
			$wiadomosc = "Nowa aktywacja HBO GO dla abonenta: " .$id . "\r\n" .
                      "Wyslano na adres e-mail: " .$mail_pomocniczy . "\r\n";
		                                 
      }  
		else if ($akcja == 'check_del')
		{
		
			$tytul = "Deaktywacja HBO GO - ID: " .$id;
			$wiadomosc = "Deaktywacja HBO GO dla abonenta: " .$id;
			
		}
		else if ($akcja == 'check_re')
		{
		
			$tytul = "Reaktywacja HBO GO - ID: " .$id;
			$wiadomosc = "Reaktywacja HBO GO dla abonenta: " .$id;
		}
		
		$naglowek = 'From: no-reply@tkchopin.pl' . "\r\n" .
					'Reply-To: ibok@tkchopin.pl' . "\r\n" .
					'X-Mailer: PHP/' . phpversion();
		
		if ($akcja == 'add' or $akcja == 'del')
		{
			echo "Próba wysłania maila do abonenta...\n";
		}

		imap_mail($mail, $tytul, $wiadomosc, $naglowek);
		
		if ($akcja == 'add' or $akcja == 'del')
		{
			echo "Wysyłanie maila do abonenta zakończone sukcesem!\n";

		}
	
	}
	
	
	public function add($id, $mail, $mail_kto)
	{	
		/*	Tworzy nowy plik produkcyjny, dodaje abonenta i powiadamia go mailem	*/
		require 'admin.php';		

		$file_old = $this->file_old('PRODTKC');
		$file_temp = $this->file_new('PRODTKC');
		
		$fileHandle = fopen($file_temp, 'a+') or die ('Nie mozna utworzyc pliku');
		copy($file_old, $file_temp) or die ('Nie mozna skopiowac pliku!');
		
		echo "Proba dodania nowego abonenta... <br />";
		echo "Używany plik produkcyjny: ".$file_old."<br />" ;
		
		$haslo = $this->password($id);
		$do_pliku = $id.','.$haslo."\r\n";
		
		echo "ID: ".$id."<br />";
		echo "Haslo: ".$haslo."<br />";
		
		fwrite($fileHandle, $do_pliku) or die ('Nie zapisano do pliku! Nie dodano abonenta!');
		fclose($fileHandle);
		
		$file_cleaned = $this->clear_customer($file_temp, $id, false);
		$file_new = $file_cleaned[0];
		
		if ($file_cleaned[1] == false)
		{
		
			echo "Dodawanie abonenta zakończone sukcesem! <br />";
			echo "Zapisano do pliku: " . $file_new."<br />";
			
		
			$this->mailto($id, $haslo, $mail, 'add', NULL);
			$this->mailto($id, $haslo, $mail_kto, 'check_add', $mail);
			$this->mailto($id, $haslo, $admin, 'check_add', $mail);
		
		}
		else 
		{
			
			echo "Abonent już miał aktywne konto!<br />";
			echo "Zapisano do pliku: " . $file_new."<br />";
		
		}
		
	} 
	
	public function re($id, $mail, $mail_kto)
	{	
		/*	Tworzy nowy plik produkcyjny, dodaje abonenta i powiadamia go mailem	*/
		require 'admin.php';		
	
		$file_old = $this->file_old('PRODTKC');
		$file_temp = $this->file_new('PRODTKC');
		
		$fileHandle = fopen($file_temp, 'a+') or die ('Nie mozna utworzyc pliku');
		copy($file_old, $file_temp) or die ('Nie mozna skopiowac pliku!');
		
		echo "Próba dodania nowego abonenta... <br />";
		echo "Używany plik produkcyjny: ".$file_old."<br />" ;
		
		$haslo = $this->password($id);
		$do_pliku = $id.','.$haslo."\r\n";
		
		echo "ID: ".$id."<br />";
		echo "Haslo: ".$haslo."<br />";
		
		fwrite($fileHandle, $do_pliku) or die ('Nie zapisano do pliku! Nie dodano abonenta!');
		fclose($fileHandle);
		
		$file_cleaned = $this->clear_customer($file_temp, $id, false);
		$file_new = $file_cleaned[0];
		
		if ($file_cleaned[1] == false)
		{
		
			echo "Reaktywacja abonenta zakończone sukcesem! <br />";
			echo "Zapisano do pliku: " . $file_new."<br />";
			
		
			if ($mail != Null)
			{
			
				$this->mailto($id, $haslo, $mail, 're');
				
			}
			$this->mailto($id, $haslo, $mail_kto, 'check_re');
			$this->mailto($id, $haslo, $admin , 'check_re');
		
		}
		else 
		{
			
			echo "Abonent już miał aktywne konto!<br />";
			echo "Zapisano do pliku: " . $file_new."<br />";
		
		}
		
	} 	
	
	public function del($id, $mail, $mail_kto)
	{
		/*	Tworzy nowy plik produkcyjny, dodaje abonenta i powiadamia go mailem	*/
		require 'admin.php';		

		$file_old = $this->file_old('PRODTKC');
		$file_temp = $this->file_new('PRODTKC');
		
		$fileHandle = fopen($file_temp, 'a+') or die ('Nie mozna otworzyc pliku');
		copy($file_old, $file_temp) or die ('Nie mozna skopiowac pliku!');
		
		echo "Próba usunięcia  abonenta... <br />";
		echo "Używany plik produkcyjny: ".$file_old."<br />" ;	
		echo "ID: ".$id."<br />";
		
		$file_cleaned = $this->clear_customer($file_temp, $id, true);
		$file_new = $file_cleaned[0];
		
		if ($file_cleaned[1] == false)
		{
		
			echo "Usuwanie abonenta zakończone sukcesem! <br />";
			echo "Zapisano do pliku: " . $file_new."<br />";
			
			if ($mail != Null)
			{
			
				$this->mailto($id, Null, $mail, 'del');
			
			}
			$this->mailto($id, Null, $mail_kto, 'check_del');
			$this->mailto($id, Null, $admin, 'check_del');
			
		}
		else 
		{
			
			echo "Abonent nie miał aktywnego konta!<br />";
			echo "Zapisano do pliku: " . $file_new."<br />";
		
		}
		
	}
	
} 

function www()
{
	$hbogo = new Generator;

	$id = $_POST['id'];
	$mail = $_POST['mail'];
	$mail_kto = $_POST['mail_kto'];
	$polecenie = $_POST['polecenie'];

   switch ($polecenie)
	{
	
		case 'add':
			if (($id != Null and $mail != Null) and (strlen($id) == 6))
			{
			
				$hbogo->add($id, $mail, $mail_kto);
			
			}
			else
			{
				
				echo "Coś poszło nie tak!";
				
			}
			break;
		
		case 'del':
			if ($id != Null and strlen($id) == 6)
			{
			
				$hbogo->del($id, $mail, $mail_kto);
			
			}
			else
			{
				
				echo "Coś poszło nie tak!";
				
			}
			break;
		
		case 're':
			if ($id != Null and strlen($id) == 6)
			{
			
				$hbogo->re($id, $mail, $mail_kto);
			
			}
			else
			{
				
				echo "Coś poszło nie tak!";
				
			}
			break;
		
	}
}

www();
?>
