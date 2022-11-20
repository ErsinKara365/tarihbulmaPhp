<?php 

/*Burada veritabanından tarih bilgisi cekilir. dakikaya cevrilir ve simdiki zaman ile dakika cinsinden farkı bulunur*/



include "connect.php";

date_default_timezone_set('Etc/GMT-0');


$kayit = $db->query("SELECT * FROM arena");
$kayit->execute();

/*Her bir dakika da bir işlem yapar */

while ($hepsi  =  $kayit->fetch(PDO::FETCH_ASSOC)){ 

	$yil = substr($hepsi["a_zaman"],0,4);
	$ay = substr($hepsi["a_zaman"],5,2);
	$gun = substr($hepsi["a_zaman"],8,2);
	$saata = substr($hepsi["a_zaman"],11,2);
	$dakikaa = substr($hepsi["a_zaman"],14,2);
	$saniye= substr($hepsi["a_zaman"],17,2);
	$fark =time() - mktime($saata,$dakikaa,$saniye,$ay,$gun,$yil) ;

	$dakika = $fark / 60;
	$saata = $dakika / 60;
	$dakika_farki = floor($dakika - (floor($saata) * 60));

	/*Zaman farkı sıfırdan büyük olursa devreye girsin son*/
	if ($dakika_farki > 0 ){ 
		/*Savunan kişi bilgilerinin cekilmesi*/
		$savunan = $db->prepare("SELECT * FROM kayit where u_id=? ");
		$savunan->execute(array($hepsi["a_savunan"]));
		$savunan_veri = $savunan->fetch(PDO::FETCH_ASSOC);
		


		/*Savunan kişinin can yükseltme*/
		if ($savunan_veri["u_can"] <= 99 ) {
			$candolum = $savunan_veri['u_can']+2;
			$guncelle = $db->prepare("UPDATE kayit SET u_can=? WHERE u_id=?");
			$guncelle->execute(array($candolum,$hepsi["a_savunan"]));		
		}

		/*Savunan kişinin saldir yükseltme*/
		if ($savunan_veri["u_saldir"] <= 99) {
			$candolum = $savunan_veri['u_saldir']+2;
			$guncelle = $db->prepare("UPDATE kayit SET u_saldir=? WHERE u_id=?");
			$guncelle->execute(array($candolum,$hepsi["a_savunan"]));
		}

		/*Savunan kişinin koru yükseltme*/
		if ($savunan_veri["u_koru"] <= 99) {
			$candolum = $savunan_veri['u_koru']+2;
			$guncelle = $db->prepare("UPDATE kayit SET u_koru=? WHERE u_id=?");
			$guncelle->execute(array($candolum,$hepsi["a_savunan"]));
		}

		

		$simdiki_zaman = date("Y-m-d H:i:s");
		$sifirla = $db->prepare("UPDATE arena SET a_zaman =? WHERE a_savunan=?");
		$sifirla->execute(array($simdiki_zaman,$hepsi["a_savunan"] ));

		

	}
}


?>
