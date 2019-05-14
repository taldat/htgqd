<?php
    if(isset($_POST['submit'])){
        if($_POST['diem'] == ""){
            $diem = 40;
        } else {
            $diem = (float) $_POST['diem'];
        }
        $truong = isset($_POST['truong']) ? $_POST['truong'] : '';
        $khoi = isset($_POST['khoi']) ? $_POST['khoi'] : '';
        $nganh = isset($_POST['nganh']) ? $_POST['nganh'] : '';
        $khuvuc = isset($_POST['khuvuc']) ? $_POST['khuvuc'] : '';
        if($_POST['hocphi'] == ""){
            $hocphi = 20000000;
        } else {
            $hocphi = (float) $_POST['hocphi'];
        }

        $conn = mysqli_connect('localhost', 'root', '123456', 'htgqd');
        if($khoi == 'A1'){
            $query = "SELECT * 
                    FROM htgqd 
                    WHERE Diemchuan <= $diem 
                    AND Khoi = '$khoi' 
                    AND Nganh LIKE '%$nganh%' 
                    AND Hocphi <= $hocphi";
        } else if($khoi != ''){
            $query = "SELECT * 
                    FROM htgqd 
                    WHERE Diemchuan <= $diem 
                    AND Khoi LIKE '%$khoi%'
                    AND Khoi != 'A1' 
                    AND Nganh LIKE '%$nganh%' 
                    AND Hocphi <= $hocphi";
        } else {
            $query = "SELECT * 
                    FROM htgqd 
                    WHERE Diemchuan <= $diem 
                    AND Nganh LIKE '%$nganh%' 
                    AND Hocphi <= $hocphi";
        }
        $result = $conn->query($query) or die($conn->error);
        $i = $j = $k = $l = 0;
        // luu cac gia tri tim duoc vao mang temp
        while($row = $result->fetch_assoc()){
            $temp[$i] = array();
            $temp[$i]['diem'] = $row['Diemchuan'];
            $temp[$i]['truong'] = $row['Truong'];
            $temp[$i]['khoi'] = $row['Khoi'];
            $temp[$i]['nganh'] = $row['Nganh'];
            $temp[$i]['khuvuc'] = $row['Khuvuc'];
            $temp[$i]['hocphi'] = $row['Hocphi'];
            $temp[$i]['chitieu'] = $row['Chitieu'];
            $i++;
        }

        $i--;

        // lay cac gia tri cho topsis
        for ($j = 0; $j <= $i; $j++){
            $topsis[$j] = array();
            $topsis[$j]['diem'] = $diem - $temp[$j]['diem'];
            $topsis[$j]['hocphi'] = $hocphi - $temp[$j]['hocphi'];
            $topsis[$j]['chitieu'] = $temp[$j]['chitieu'];
            if($temp[$j]['khuvuc'] == $khuvuc || $khuvuc == ''){
                $topsis[$j]['khuvuc'] = 1;
            } else {
                $topsis[$j]['khuvuc'] = 0;
            }
        }

        // tinh toan mau so cho chuan hoa vector
        $ms = array();
        $ms['diem'] = 0;
        $ms['hocphi'] = 0;
        $ms['chitieu'] = 0;
        $ms['khuvuc'] = 0;
        for ($j = 0; $j <= $i; $j++){
            $ms['diem'] += pow($topsis[$j]['diem'],2);
            $ms['hocphi'] += pow($topsis[$j]['hocphi'],2);
            $ms['chitieu'] += pow($topsis[$j]['chitieu'],2);
            $ms['khuvuc'] += pow($topsis[$j]['khuvuc'],2);
        }
        $ms['diem'] = sqrt($ms['diem']);
        $ms['hocphi'] = sqrt($ms['hocphi']);
        $ms['chitieu'] = sqrt($ms['chitieu']);
        $ms['khuvuc'] = sqrt($ms['khuvuc']);

        //tinh toan bang topsis voi trong so la 0.4, 0.3, 0.2, 0.1
        for ($j = 0; $j <= $i; $j++){
            $topsis[$j]['diem'] = number_format($topsis[$j]['diem']*0.4/$ms['diem'],4);
            $topsis[$j]['hocphi'] = number_format($topsis[$j]['hocphi']*0.3/$ms['hocphi'],4);
            $topsis[$j]['chitieu'] = number_format($topsis[$j]['chitieu']*0.2/$ms['chitieu'],4);
            $topsis[$j]['khuvuc'] = number_format($topsis[$j]['khuvuc']*0.1/$ms['khuvuc'],4);
            /*echo $topsis[$j]['diem'];
            echo " ".$topsis[$j]['hocphi'];
            echo " ".$topsis[$j]['chitieu'];
            echo " ".$topsis[$j]['khuvuc']."<br>";*/
        }

        // tim giai phap ly tuong tot va ly tuong xau (max, min)
        $max = array();
        $min = array();
        $max['diem']  = $max['hocphi'] = $max['chitieu'] = $max['khuvuc'] = $max['C'] = 0;
        $min['diem'] = $min['hocphi'] = $min['chitieu'] = $min['khuvuc'] = 1;
        for ($j = 0; $j <= $i; $j++){
            if($topsis[$j]['diem'] > $max['diem']) {
                $max['diem'] = $topsis[$j]['diem'];
            }
            if($topsis[$j]['hocphi'] > $max['hocphi']){
                $max['hocphi'] = $topsis[$j]['hocphi'];
            }
            if($topsis[$j]['chitieu'] > $max['chitieu']){
                $max['chitieu'] = $topsis[$j]['chitieu'];
            }
            if($topsis[$j]['khuvuc'] > $max['khuvuc']){
                $max['khuvuc'] = $topsis[$j]['khuvuc'];
            }

            if($topsis[$j]['khuvuc'] < $min['khuvuc']){
                $min['khuvuc'] = $topsis[$j]['khuvuc'];
            }
            if($topsis[$j]['diem'] < $min['diem']){
                $min['diem'] = $topsis[$j]['diem'];
            }
            if($topsis[$j]['hocphi'] < $min['hocphi']){
                $min['hocphi'] = $topsis[$j]['hocphi'];
            }
            if($topsis[$j]['chitieu'] < $min['chitieu']){
                $min['chitieu'] = $topsis[$j]['chitieu'];
            }
        }

        // tinh khoang cach toi cac phuong an tot, xau va do do tuong tu
        for ($j = 0; $j <= $i; $j++){
            $topsis[$j]['tot'] = number_format(sqrt(pow($topsis[$j]['diem'] - $max['diem'], 2)
                                    +pow($topsis[$j]['hocphi'] - $max['hocphi'], 2)
                                    +pow($topsis[$j]['chitieu'] - $max['chitieu'], 2)
                                    +pow($topsis[$j]['khuvuc'] - $max['khuvuc'], 2)), 4);

            $topsis[$j]['xau'] = number_format(sqrt(pow($topsis[$j]['diem'] - $min['diem'], 2)
                                    +pow($topsis[$j]['hocphi'] - $min['hocphi'], 2)
                                    +pow($topsis[$j]['chitieu'] - $min['chitieu'], 2)
                                    +pow($topsis[$j]['khuvuc'] - $min['khuvuc'], 2)), 4);

            $topsis[$j]['C'] = $topsis[$j]['xau']/($topsis[$j]['xau'] + $topsis[$j]['tot']);
        }

        //ket qua
        for($j = 0; $j <= $i; $j++){
            if($temp[$j]['truong'] == $truong){
                $kq[$k] = array();
                $kq[$k]['diem'] = $temp[$j]['diem'];
                $kq[$k]['truong'] = $temp[$j]['truong'];
                $kq[$k]['khoi'] = $temp[$j]['khoi'];
                $kq[$k]['nganh'] = $temp[$j]['nganh'];
                $kq[$k]['khuvuc'] = $temp[$j]['khuvuc'];
                $kq[$k]['hocphi'] = $temp[$j]['hocphi'];
                $kq[$k]['chitieu'] = $temp[$j]['chitieu'];
                $k++;
                $topsis[$j]['C'] = 0;
            }
        }

        $vitri = 0;
        for($l = 0; $l <= $i; $l++){
            for($j = 0; $j <= $i; $j++){
                if($topsis[$j]['C'] > $max['C']){
                    $max['C'] = $topsis[$j]['C'];
                    $vitri = $j;
                }
            }
            $max['C'] = 0;
            $topsis[$vitri]['C'] = 0;
            $kq[$k] = array();
            $kq[$k]['diem'] = $temp[$vitri]['diem'];
            $kq[$k]['truong'] = $temp[$vitri]['truong'];
            $kq[$k]['khoi'] = $temp[$vitri]['khoi'];
            $kq[$k]['nganh'] = $temp[$vitri]['nganh'];
            $kq[$k]['khuvuc'] = $temp[$vitri]['khuvuc'];
            $kq[$k]['hocphi'] = $temp[$vitri]['hocphi'];
            $kq[$k]['chitieu'] = $temp[$vitri]['chitieu'];
            if($k >= 4){
                break;
            } else{
                $k++;
            }
        }
        for($l = 0; $l <= 4; $l++){
            echo $kq[$l]['truong']."  ";
            echo $kq[$l]['nganh']."  ";
            echo $kq[$l]['khuvuc']."<br>";
        }
    }
?>
