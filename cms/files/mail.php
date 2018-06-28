<?
class mime_mail {
    var $parts;
    var $to;
    var $from;
    var $headers;
    var $subject;
    var $body;
    var $replyTo;
    var $boundary;

    function __construct() {
        $this->parts = array();
        $this->to =  "";
        $this->from =  "";
        $this->subject =  "";
        $this->body =  "";
        $this->headers =  "";
        $this->replyTo = "";
    }

    function mime($str, $data_charset='utf-8', $send_charset='utf-8')
    {
        if($data_charset != $send_charset)
        {
            $str = iconv($data_charset, $send_charset, $str);
        }

        return '=?' . $send_charset . '?B?' . base64_encode($str) . '?=';
    }

    function add_attachment($message, $name = "", $ctype = "image/jpeg"){
        $this->parts [] = array (
            "ctype" => $ctype,
            "message" => $message,
            "name" => $name);
    }

    function build_message($part) {
        $message = $part["message"];
        $message = chunk_split(base64_encode($message));
        $encoding = "base64";
        return "Content-Type: ".$part["ctype"].($part["name"]? "; name = \"".$part["name"]."\"" : "")."\nContent-Transfer-Encoding: $encoding\n\n$message\n";
    }

    function getBoundary(){
        if(!$this->boundary){
            $this->boundary = "b".md5(uniqid(time()));
        }
        return $this->boundary;
    }

    function build_multipart() {

        $multipart = "Content-Type: multipart/mixed; boundary = ".$this->getBoundary()."\n\nThis is a MIME encoded message.\n\n--".$this->getBoundary();

        foreach($this->parts as $v) $multipart .= "\n".$this->build_message($v). "--".$this->getBoundary();
        if (!empty($this->body)) $multipart .= "\n".$this->build_message( array('message'=>$this->body, 'name'=>'', 'ctype'=>'text/html; charset=utf-8') ). "--".$this->getBoundary();
        $multipart .=  "--\n";
        return $multipart;
    }

    function send($mail) {
        $mime = "";
        if (!empty($this->from)) $mime .= "From: ".$this->from. "\nReply-To: ".(!empty($this->replyTo) ? $this->replyTo : $this->from)."\n";
        if (!empty($this->headers)) $mime .= $this->headers. "\n";

        $mime .= "MIME-Version: 1.0\n";
        $mime .= "Precedence: bulk\n";
        $mime .= "Content-Type: multipart/mixed; boundary=\"".$this->getBoundary()."\"\n";

       return @mail($mail, $this->mime($this->subject), $this->build_multipart(), $mime);
    }
}
?>