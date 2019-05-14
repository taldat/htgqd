<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <form action="result.php" method="POST">
        <div>
            <h1><b>Trợ giúp chọn trường đại học, cao đẳng</b></h1>
            <table style="margin: auto">
                <tr>
                    <td><label class="label"  for="diem">Điểm:</td>
                    <td><input class="input" type="number" step="0.01" name="diem" min="0" autocomplete="false" placeholder="Nhập điểm thi của bạn"></td>
                </tr>
                <tr>
                    <td><label class="label"  for="truong">Trường:</td>
                    <td>
                        <input class="input" list="truong" name="truong" placeholder="Nhập trường mong muốn" >
                        <datalist id="truong" >
                            <?php
                                $conn = mysqli_connect('localhost', 'root', '123456', 'htgqd');
                                $query = "SELECT DISTINCT truong FROM htgqd";
                                $result = mysqli_query($conn,$query);
                                if($result->num_rows > 0) {
                                    while($row = $result->fetch_assoc()) {
                            ?>
                            <option value="<?php echo $row['truong']; ?>">
                            <?php
                                    }
                                }
                            ?> 
                    </td>
                </tr>
                <tr>
                    <td><label class="label"  for="khoi">Khối:</td>
                    <td><input class="input" type="text" name="khoi" placeholder="Nhập khối (tích nếu có môn nhân đôi điểm)"></td>
                    <td><input class="input" type="checkbox" name="ghichu"></td>
                </tr>
                <tr>
                    <td><label class="label"  for="nganh">Ngành:</td>
                    <td><input class="input" type="text" name="nganh" placeholder="Nhập ngành mong muốn"></td>
                </tr>
                <tr>
                    <td><label class="label"  for="khuvuc">Khu Vực:</td>
                    <td>
                        <input class="input" list="khuvuc" name="khuvuc" placeholder="Nhập khu vực mong muốn">
                        <datalist id="khuvuc" >
                            <?php
                                $query = "SELECT DISTINCT khuvuc FROM htgqd";
                                $result = mysqli_query($conn,$query);
                                if($result->num_rows > 0) {
                                    while($row = $result->fetch_assoc()) {
                            ?>
                            <option value="<?php echo $row['khuvuc']; ?>">
                            <?php
                                    }
                                }
                            ?> 
                    </td>
                </tr>
                <tr>
                    <td><label class="label"  for="hocphi">Học Phí:</td>
                    <td><input class="input" type="number" name="hocphi" autocomplete="false" min="0" placeholder="Nhập học phí mong muốn (theo kì)"></td>
                </tr>
                <tr>
                    <td><button class="button" type="submit" name="submit" >Tìm Trường</button></td>
                </tr>
            </table>
        </div>
    </form>     
</body>
</html>