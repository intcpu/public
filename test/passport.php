<?php

    //身份证号验证
    function checkIdCard($idcard){
        //长度验证
        if(!preg_match('/^\d{17}(\d|x)$/i',$idcard))
        {
            return false;
        }

        // 判断是否大于2078年，小于1900年
        $year = substr($idcard,6,4);
        if ($year<1900 || $year>2078)
        {
            return false;
        }

        //身份证编码规范验证
        $idcard_base = substr($idcard,0,17);
        //加权因子
        $factor = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);
        
        //校验码对应值
        $verify_number_list = ['1', '0', 'X', '9', '8', '7', '6', '5', '4','3', '2'];
        $checksum = 0;
        for ($i = 0; $i < strlen($idcard_base); $i++)
        {
            $checksum += substr($idcard_base, $i, 1) * $factor[$i];
        }

        $mod = $checksum % 11;

        $verify_number = $verify_number_list[$mod];


        if(strtoupper(substr($idcard,17,1)) != $verify_number)
        {
           return false;
        }

        return true;
    }

    var_dump(checkIdCard(411526198810236715));

?>