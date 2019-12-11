<?php
namespace Library\Mail;
class Email {
	/**
	 *  发送邮箱
	 * @param String $emailHost 您的企业邮局域名
	 * @param String $emailUserName 邮局用户名(请填写完整的email地址)
	 * @param String $emailPassWord 邮局密码
	 * @param String $formName 邮件发送者名称
	 * @param String $email  收件人邮箱，收件人姓名
	 * @param String $title	发送标题
	 * @param String $body	发送内容
	 * @return boolean
	 */
	public static function send($emailHost,$emailUserName,$emailPassWord,$formName,$email,$title,$body) {
	    // 以下内容为发送邮件
	    $mail=new PHPMailer();//建立邮件发送类
	    $mail->IsSMTP();//使用SMTP方式发送 设置设置邮件的字符编码，若不指定，则为'UTF-8
	    $mail->Host=$emailHost;//'smtp.qq.com';//您的企业邮局域名
	    $mail->SMTPAuth=true;//启用SMTP验证功能   设置用户名和密码。
	    $mail->Username=$emailUserName;//'mail@koumang.com'//邮局用户名(请填写完整的email地址)
	//    $mail->Username='admin@shikeh.com';//邮局用户名(请填写完整的email地址)
	//    $mail->Password='WWW15988999998com';//邮局密码
	    $mail->Password=$emailPassWord;//'xiaowei7758258'//邮局密码
	    $mail->From=$emailUserName;//'mail@koumang.com'//邮件发送者email地址
	    $mail->FromName=$formName;//邮件发送者名称
	    $mail->AddAddress($email);// 收件人邮箱，收件人姓名
	    //$mail->AddBCC('chnsos@126.com',$_SESSION['clean']['name']);//收件人地址，可以替换成任何想要接收邮件的email信箱,格式是AddAddress("收件人email","收件人姓名")
	    $mail->IsHTML(true); // set email format to HTML //是否使用HTML格式
	    $mail->Subject="=?UTF-8?B?".base64_encode($title)."?=";
	    $mail->Body=$body; //邮件内容
	    $mail->AltBody = "这是一封HTML格式的电子邮件。"; //附加信息，可以省略
	    $mail->Send();
	    return $mail->ErrorInfo;
	}
}