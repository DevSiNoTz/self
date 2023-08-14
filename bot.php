<?php
date_default_timezone_set('Asia/Tehran');
use function Amp\File\{write,read};
use danog\MadelineProto\Logger;
use danog\MadelineProto\Settings;
use danog\Loop\GenericLoop as loop;
use danog\MadelineProto\FileCallback as progress;
use danog\MadelineProto\EventHandler as EH;
use danog\MadelineProto\RPCErrorException as err;


if (is_file('vendor/autoload.php')) {
    include 'vendor/autoload.php';
} else {
    if (!is_file('madeline.php')) {
        copy('https://phar.madelineproto.xyz/madeline.php', 'madeline.php');
    }
    include 'madeline.php';
}

$DB_A = "DB.json";
if (!file_exists($DB_A)) {
    $Base = [
        "Eemoji"      => [0 => 1],
        "Mutes"      => [0 => 1],
        "Enemys"      => [0 => 1],
        "PostID"      => [0 => 1],
        'FilterList' =>   [],
        'spam'       => ['limit' => 5],
        'protection' => 'off',
        'antipv'     => 'off',
    ];
    write($DB_A, json_encode($Base, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}
function DB($data = null)
{
    if ($data == null) {
        return json_decode(read('DB.json'), true);
    } else {
        write('DB.json', json_encode($data, 128 | 256));
        return "Success !";
    }
}


function reactions(...$actions)
{
$o = [];
foreach($actions as $string)
{
 $o[] = ['_' => 'reactionEmoji', 'emoticon' => $string];
}
return $o;
}
function bytesShortener($bytes, int $round = 0): string
{
    $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
    $index = 0;
    while ($bytes > 1024) {
        $bytes /= 1024;
        if (++$index === 8)
            break;
    }
    if ($round !== 0) {
        $bytes = round($bytes, $round);
    }
    return "$bytes {$units[$index]}";
}
function getCpuCores(): int
{
    return (int) (PHP_OS_FAMILY === 'Windows'
        ? getenv('NUMBER_OF_PROCESSORS')
        : substr_count(read('/proc/cpuinfo'), 'processor'));
}

function mediaTimeDeFormater($seconds)
{
    if (!is_numeric($seconds))
        throw new Exception("Invalid Parameter Type!");
        $m = 'm';
        $s = 's';
        $h = 'h';
        $d='d';
    $ret = "";
    $hours = (string)floor($seconds / 3600);
    $secs = (string)$seconds % 60;
    $mins = (string)floor(($seconds - ($hours * 3600)) / 60);
    $ho = (string)floor($seconds / 3600%24);
    $days = floor(($hours / 24));
    if (strlen($hours) == 1)
        $hours = "0" . $hours;
    if (strlen($secs) == 1)
        $secs = "0" . $secs;
    if (strlen($mins) == 1)
        $mins = "0" . $mins;
    if ($hours == 0)
        $ret = "$mins$m:$secs$s";
    else
        $ret = "$ho$h:$mins$m:$secs$s";
    if($days>0){
        $ret= "$days$d, ".$ret;
    }
    return $ret;
}


class MP extends EH {
  
  const Report = "PHPLoop";
  const Admins = [6034230554];
  
  const Api    = [
                'id'   => 2054530,
                'hash' => 'c56641e324ae0feb90fcfbe472ad0215'
                 ];
  
  public function getReportPeers()
    {
        return [self::Report];
    }
  private $time;
  private $loop;
  public function onStart()
    {
        $this->time = time();
        $loop       = $this;
        $this->loop = new loop(
            function () use ($loop) {
            $font = ['０','１','２','３','４','５','６','７','８','９'];
            $clock = str_replace(range(0,9),$font,date('H⌯i'));
                return 60;
            },
            'Myloop'
        );
        $this->loop->start();
    }
    
  public function onUpdateNewChannelMessage(array $update)
    {
        return $this->onUpdateNewMessage($update);
    }
    public function onUpdateNewMessage(array $update)
    {
      if ($update['message']['_'] !== 'message' || $update['message']['date'] <= $this->time) {
            return;
        }
        
      try{
        
        
        $message = $update['message'];
        $msgId   = $message['id']      ?? null;
        $isOut   = $message['out']     ?? false;
        $text    = $message['message'] ?? '';
        $fromId  = $message['from_id']['user_id']          ?? null;
        $replyTo = $message['reply_to']['reply_to_msg_id'] ?? null;
        $peer    = $this->getID($update);
        $me = $this->getSelf();
        $❌='<a href="emoji:5972223227055836399">🔻</a>';
        $me_id = $me['id'];
        $user_id = isset($update['message']['from_id']['user_id']) ? $update['message']['from_id']['user_id'] : null;
        @$DB =  DB();
       $info      =  $this->getInfo($update);
$type      = $info['type'];
       /* if($peer == "-1001635659099"){
        $reactions = reactions("👍","💘","🕊");
         $this->messages->sendReaction(
         peer: "-1001635659099", 
         msg_id: $msgId, 
         reaction:$reactions
          );
        }*/
        $🔸='<a href="emoji:5971796410385829300">🔸</a>';
$💠='<a href="emoji:5972185809300753162">💠</a>';
$👑='<a href="emoji:5852931201899171394">👑</a>';

$yes= '<a href="emoji:5041787307623973734">👍</a>';
$no='<a href="emoji:5042174988551983379">👎</a>';
if (in_array($user_id, $DB['Mutes'])) {
$this->del($msgId,$peer);
}
/*if (in_array($user_id, $DB['Eemoji'])) {
$reactions = reactions("🤮","🤡","💩");
$this->messages->sendReaction(
peer: $peer,
msg_id: $msgId, 
reaction:$reactions
);
}*/
if (isset($text)){
if (isset($DB['FilterList'][$user_id])){
foreach($DB['FilterList'][$user_id] as $res){
if(strstr($text, $res)){
$this->del($msgId,$peer);
}
}}
}
if(isset($message['media']['ttl_seconds'])){
$time=time();
$time = date("Y/m/d - H:i:s", $time);

$doni = $this->messages->getMessages([
'peer' => $peer, 
'id' => [$msgId]
]);

$file = isset($message['media']) ? $message['media'] : "none";  
if($file != "none"){
$metime = $file['ttl_seconds'];
$output_file_name = $this->downloadToFile($file, getcwd(). '/SiNoTz.jpg');
 $this->messages->sendMedia(
peer: $me_id, 
media:['_' => 'inputMediaUploadedDocument','file'=>'SiNoTz.jpg','attributes'=>[['_' => 'documentAttributeFilename', 
'file_name'=>'SiNoTz.jpg']]],
message: "<strong>├ • Downloader { DESTRUCTING MEDIA }
├ • Media Time (<code>$metime s</code>)
├ • Download Time (<code>$time</code>)
├ • Current ChatID ↬ (<code>$peer</code>)
├ • Developer ↬ (@DevSiNo $💠)
├ • ┅┅━━━━ 𖣫 ━━━━┅┅ •</strong>",
parse_mode:'html'
);
}
unlink("SiNoTz.jpg");
}
if($fromId=="6269235469" and $text =="👇👇👇👇"){
$id = $msgId;
write("1.txt","$id");
}
if($fromId=="6269235469" and $text =="end"){
$id = $msgId;
write("2.txt","$id");
}


if($fromId=="6269235469" and $text =="end"){
if(file_exists("IDD.txt")){

if($text=="❌ Try Again ❌"){
unlink("IDD.txt");
$this->messages->forwardMessages(
drop_author: true, 
from_peer:$peer,
 to_peer: $IDD,
  id:$arr,
  );
  }
$x1=read("1.txt");
$x2=read("2.txt");
$ID= ($x2-$x1);
$number = $x1;
$arr=[];
for ($i = 0; $i < $ID-1; $i++) {
    $number += 1;
$arr[]=$number;
}
$t=json_encode($arr);
$IDD=read("IDD.txt");
$this->messages->forwardMessages(
drop_author: true, 
from_peer:$peer,
 to_peer: $IDD,
  id:$arr,
  );
}
if($text=="end"){
if(file_exists("IDD.txt")){
unlink("IDD.txt");
}
}

}


if($fromId=="6269235469"){
if(preg_match('(https://trfamily.ir/TzNullBoT/dataa/)',$text)){
if(file_exists("IDD.txt")){
$data = [
    'api_token' => '8355e54117c8f348ebfc16eceb5c9c64',
    'url' => "$text",
    'return' => 'apple_music,spotify',
];
$ch = curl_init();
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_URL, 'https://api.audd.io/');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
$result = curl_exec($ch);
curl_close($ch);
$x=json_decode($result,true);
$pe =read("IDD.txt");
if($x['result'] === null){
$h="<strong>Error , i need more seconds</strong>";
$this->send($pe,$h,$msgId);
}else{
$timer = $x['result']['timecode'];
$release_date= $x['result']['release_date'];
$title= $x['result']['title'];
$artist= $x['result']['artist'];
$link= $x['result']['apple_music']['previews'][0]['url'];

$this->messages->sendMedia([
'peer' => $pe,
'media' => [
'_' => 'inputMediaUploadedDocument',
'file' => "$link",
'attributes' => [
['_' => 'documentAttributeAudio', 
'voice' => false, 
'title' => "$title", 
'performer' => "$artist"],
],
],
'message' => "
<strong>├ • Name ↬ (<code>$title</code>)
├ • Artist Name ↬ (<code>$artist</code>)
├ • Time ↬ (<code>$timer</code>)
├ • Release date↬ (<code>$release_date</code>)
├ • Current ChatID ↬ (<code>$pe</code>)
├ • Developer ↬ (@DevSiNo $💠)
├ • ┅┅━━━━ 𖣫 ━━━━┅┅ •</strong>
",
'parse_mode' => 'html',
]);
unlink("IDD.txt");
}}}}
if (isset($DB['protection']) && $DB['protection'] == 'on') {
#Anti Spam
if ($user_id != $me_id) {
if (isset($message) && $type == 'user') {
if(isset($DB['spam'][$user_id])){
if (@$DB['spam'][$user_id] != 'VIP') {
@$num   = @$DB['spam'][$user_id] + 1;
@$limit = @$DB['spam']['limit'];
@$res   = $limit - $num;
@$num   = ($num == 6) ? 1 : $num;
$gg= $DB['PostID'][$user_id];
if($gg!="none"){
$this->del($gg,$peer);
}
$name= $this->getInfo($user_id)['User']['first_name'];
$🔴='<a href="emoji:5971867376130461576">🔻</a>';
$us='<a href="emoji:5974038293120027938">😀</a>';
$❗️='<a href="emoji:5213195952008997792">😀</a>';
$h="<strong>$us Hi <a href='mention:$user_id'>$name</a> 

$❗️ Protection [warn:$num/$limit] 

$❌ Note : if you send me 5 messages i will block you</strong>";
$sent=$this->msg($update,$h,$msgId);
$sent_id         = isset($sent['updates'][2]['message']['id']) ? $sent['updates'][2]['message']['id']                         : $sent['updates'][1]['message']['id'];
if (@$DB['spam'][$user_id] < @$DB['spam']['limit']) {
@$DB['spam'][$user_id] += 1;
@$DB['PostID'][$user_id]="$sent_id";
DB($DB);
} else {
@$DB['spam'][$user_id] = 1;
@$DB['PostID'][$user_id]="0";
DB($DB);
}


}
}else{
@$DB['spam'][$user_id] = 0;
@$DB['PostID'][$user_id]="none";
DB($DB);
if (@$DB['spam'][$user_id] != 'VIP') {
@$num   = @$DB['spam'][$user_id] + 1;
@$limit = @$DB['spam']['limit'];
@$res   = $limit - $num;
@$num   = ($num == 6) ? 1 : $num;
$name= $this->getInfo($user_id)['User']['first_name'];
$🔴='<a href="emoji:5971867376130461576">🔻</a>';
$us='<a href="emoji:5974038293120027938">😀</a>';
$❗️='<a href="emoji:5213195952008997792">😀</a>';
$h="<strong>$us Hi <a href='mention:$user_id'>$name</a> 

$❗️ Protection [warn:$num/$limit] 

$❌ Note : if you send me 5 messages i will block you</strong>";
$sent=$this->msg($update,$h,$msgId);
$sent_id         = isset($sent['updates'][2]['message']['id']) ? $sent['updates'][2]['message']['id']                         : $sent['updates'][1]['message']['id'];
@$DB['spam'][$user_id] += 1;
@$DB['PostID'][$user_id]="$sent_id";
DB($DB);
}
}
}
}
if (isset($DB['spam'][$user_id]) && $type == 'user') {
if (@$DB['spam'][$user_id] != 'VIP') {
if (@$DB['spam'][$user_id] == @$DB['spam']['limit']) {
$name=$this->getInfo($user_id)['User']['first_name'];
$gg= $DB['PostID'][$user_id];
$this->del($gg,$peer);
$🔴='<a href="emoji:5971867376130461576">🔻</a>';
$⛔='<a href="emoji:5852487725051023314">⛔</a>';
$h="<strong>$🔴 User <a href='mention:$user_id'>$name</a> $⛔ if i remember correctly I mentioned in my previous message that this is not the right place for you to spam.
Though you ignored that message.
So, I simply blocked you. 
Now you can't do anything unless my master comes online and unblocks you.

Developer : @SiNo_Tz $💠</strong>";
$this->msg($update,$h,$msgId);
 $this->contacts->block(['id' => $user_id]);
 @$DB['PostID'][$user_id]="0";
@$DB['spam'][$user_id] = "0";
DB($DB);
}}}}
if($fromId == "$me_id"){ // COMTZ

if (preg_match('/^[\#\!\.\/]?(data)$/i', $text)) {
$domain = 'tcp://149.154.167.51';
$port = 443;
$starttime = microtime(true);
$file = fsockopen($domain, $port, $s, $s, 1);
$stoptime = microtime(true);
fclose($file);
$ping = floor(($stoptime - $starttime) * 1000);
$load         = sys_getloadavg()[0];
$mem_usage    = round((memory_get_usage() / 1024) / 1024, 1) . 'MB';
$ver = phpversion();
$cpu = getCpuCores();
$BOTMEM=bytesShortener(memory_get_usage(), 2);
$BOTMEMMAX= bytesShortener(memory_get_peak_usage(), 2);
 $Allocated = bytesShortener(memory_get_usage(true), 2);
 $AllocatedMax = bytesShortener(memory_get_peak_usage(true), 2);
 $h="<strong>├ • Information SERVER $👑
├ • Load ↬ (<code>$load ms</code>)
├ • Ping Telegram ↬ (<code>$ping ms</code>)
├ • CPU Cores ↬ (<code>$cpu</code>)
├ • BOT Memory Usage ↬ (<code>$BOTMEM</code>)
├ • BOT MAX Memory Usage ↬ (<code>$BOTMEMMAX</code>)
├ • Allocated Memory from sys ↬ (<code>$Allocated</code>)
├ • Allocated MAX Memory from sys ↬ (<code>$AllocatedMax</code>)
├ • PHP Version ↬ (<code>$ver</code>)
├ • Source Version ↬ (<code>2.0</code>)
├ • Developer ↬ (@DevSiNo $💠)
├ • ┅┅━━━━ 𖣫 ━━━━┅┅ •</strong>";
$this->msg($update,$h,$msgId);
}
if (preg_match('/^[\#\!\.\/]?(music)$/i', $text)) {
if (isset($replyTo)) {

$g="- Waiting $🔸";
$this->edit($peer,$g,$msgId);

write("IDD.txt",$peer);
$this->messages->forwardMessages(
from_peer:$peer,
 to_peer: 6269235469,
  id:[$replyTo]
  );
}
}
if (preg_match('/^[\#\!\.\/]?(mute2|خفه)$/i', $text)) {
if (isset($replyTo)) {
if ($type == 'channel' || $type == 'supergroup') {
$rpf = yield $this->channels->getMessages(['channel' => $peer, 'id' => [$replyTo]])['users'][0];
$rpfi = $rpf['id'];
$rpfn = $rpf['first_name'];
} else {
$rpf = yield $this->messages->getMessages(['channel' => $peer, 'id' => [$replyTo]])['users'][0];
$rpfi = $rpf['id'];
$rpfn = $rpf['first_name'];
}
}else{
$rpfn=$this->getInfo($peer)['User']['first_name'];
$rpfi=$peer;
}
$🚫='<a href="emoji:5888972424258522633">🚫</a>';
if($text=="خفه"){
$h = "<strong>$❌ کاربر </strong><a href='mention:$rpfi'>$rpfn</a> $🚫 <strong> برای همیشه خفه شد :)</strong>";
}else{
$h = "<strong>$❌ This User </strong><a href='mention:$rpfi'>$rpfn</a> $🚫 <strong> Successfully Muted V:2.0.0</strong>";
}
$this->msg($update,$h,$msgId);
array_push($DB['Mutes'], $rpfi);
DB($DB);
}
if (preg_match('/^[\#\!\.\/]?(unmute2)$/i', $text)) {
if (isset($replyTo)) {
if ($type == 'channel' || $type == 'supergroup') {
$rpf = yield $this->channels->getMessages(['channel' => $peer, 'id' => [$replyTo]])['users'][0];
$rpfi = $rpf['id'];
$rpfn = $rpf['first_name'];
} else {
$rpf = yield $this->messages->getMessages(['channel' => $peer, 'id' => [$replyTo]])['users'][0];
$rpfi = $rpf['id'];
$rpfn = $rpf['first_name'];
}
}else{
$rpfn=$this->getInfo($peer)['User']['first_name'];
$rpfi=$peer;
}
$s = array_search($rpfi, $DB['Mutes']);
$s = ($s == false) ? 'no' : $s;
if ($s != 'no') {
$mu='<a href="emoji:5215668805199473901">😀</a>';
$h = "<strong>$❌ This User </strong><a href='mention:$rpfi'>$rpfn</a> $mu <strong> UnMuted V:2.0.0</strong>";
$this->msg($update,$h,$msgId);
unset($DB['Mutes'][$s]);
DB($DB);
}else{
$h = "<strong>$❌ This User </strong><a href='mention:$rpfi'>$rpfn</a><strong> NotMuted :/ </strong>";
$this->msg($update,$h,$msgId);
}}
// Enemy


if (preg_match('/^[\#\!\.\/]?(Enemy)$/i', $text)) {
if (isset($replyTo)) {
if ($type == 'channel' || $type == 'supergroup') {
$rpf = yield $this->channels->getMessages(['channel' => $peer, 'id' => [$replyTo]])['users'][0];
$rpfi = $rpf['id'];
$rpfn = $rpf['first_name'];
} else {
$rpf = yield $this->messages->getMessages(['channel' => $peer, 'id' => [$replyTo]])['users'][0];
$rpfi = $rpf['id'];
$rpfn = $rpf['first_name'];
}
}else{
$rpfn=$this->getInfo($peer)['User']['first_name'];
$rpfi=$peer;
$🚫='<a href="emoji:5888972424258522633">🚫</a>';
$h = "<strong>$👑 Successfully.
$❌This User </strong><a href='mention:$rpfi'>$rpfn</a> Added From Enemy list $🚫 <strong> </strong>";
}
$this->msg($update,$h,$msgId);
array_push($DB['Enemys'], $rpfi);
DB($DB);
}
if (preg_match('/^[\#\!\.\/]?(unEnemy|حذف دشمن)$/i', $text)) {
if (isset($replyTo)) {
if ($type == 'channel' || $type == 'supergroup') {
$rpf = yield $this->channels->getMessages(['channel' => $peer, 'id' => [$replyTo]])['users'][0];
$rpfi = $rpf['id'];
$rpfn = $rpf['first_name'];
} else {
$rpf = yield $this->messages->getMessages(['channel' => $peer, 'id' => [$replyTo]])['users'][0];
$rpfi = $rpf['id'];
$rpfn = $rpf['first_name'];
}
}else{
$rpfn=$this->getInfo($peer)['User']['first_name'];
$rpfi=$peer;
}
$s = array_search($rpfi, $DB['Enemys']);
$s = ($s == false) ? 'no' : $s;
if ($s != 'no') {
$mu='<a href="emoji:5215668805199473901">😀</a>';
$h = "<strong>$👑 Successfully.
$❌ This User </strong><a href='mention:$rpfi'>$rpfn</a> $mu <strong> Deleted From list</strong>";
$this->msg($update,$h,$msgId);
unset($DB['Enemys'][$s]);
DB($DB);
}else{
$h = "<strong>$❌ This User </strong><a href='mention:$rpfi'>$rpfn</a><strong> NotMuted :/ </strong>";
$this->msg($update,$h,$msgId);
}}




// Enemy Emoji 
if (preg_match('/^[\#\!\.\/]?(Eemoji)$/i', $text)) {
if (isset($replyTo)) {
if ($type == 'channel' || $type == 'supergroup') {
$rpf = yield $this->channels->getMessages(['channel' => $peer, 'id' => [$replyTo]])['users'][0];
$rpfi = $rpf['id'];
$rpfn = $rpf['first_name'];
} else {
$rpf = yield $this->messages->getMessages(['channel' => $peer, 'id' => [$replyTo]])['users'][0];
$rpfi = $rpf['id'];
$rpfn = $rpf['first_name'];
}
}else{
$rpfn=$this->getInfo($peer)['User']['first_name'];
$rpfi=$peer;
}

$h = "<strong>$❌ This User </strong><a href='mention:$rpfi'>$rpfn</a><strong> Set 💩 Enemy </strong>";
$this->msg($update,$h,$msgId);
array_push($DB['Eemoji'], $rpfi);
DB($DB);
}
if (preg_match('/^[\#\!\.\/]?(tiktok|tik) (.*)$/i', $text,$m)) {
$rpg=$m[2];
$g="- Waiting $🔸";
$this->edit($peer,$g,$msgId);
$h=$rpg;
write("IDD.txt",$peer);
$this->send("6269235469","$h","$msgId");
}

if (preg_match('/^[\#\!\.\/]?(insta) (.*)$/i', $text,$m)) {
$rpg=$m[2];
if(preg_match('(https://www.instagram.com/)',$rpg)){

$g="- Waiting $🔸";
$this->edit($peer,$g,$msgId);
$h=$rpg;
write("IDD.txt",$peer);
$this->send("6269235469","$h","$msgId");
}else{
$g="- Waiting $🔸";
$this->edit($peer,$g,$msgId);

$id = str_replace("@", "", $m[2]);
$url = read("https://api2.haji-api.ir/instainfo/?text=$id");
$r = json_decode($url, true);
$gg = $r['result'][0]["hd_profile_pic_url_info"]['url'];
$is_verified = ($r['result'][0]["is_verified"] == false)  ? "$no"   : "$yes";
$data=json_decode(read("https://trfamily.ir/insta.php?User=$id&Mode=info"),true);
$id = $data['result']['id'];
$username = $data['result']['username'];
$isPrivate = ($data['result']['is_private'] == false)  ? "$no"   : "$yes";
$profilePicUrl = $data['result']['profile_pic_url'];
$biography = $data['result']['biography'];
$fullName = $data['result']['full_name'];
$mediaCount = $data['result']['edge_owner_to_timeline_media']['count'];
$followedByCount = $data['result']['edge_followed_by']['count'];
$followCount = $data['result']['edge_follow']['count'];

 $link = 'https://instagram.com/' . $username;
$mentionLink = '<a href="' . htmlspecialchars($link) . '">' . htmlspecialchars($fullName) . '</a>';
$inforamtion="<strong>├ • instagram information OF $mentionLink</strong> $👑<strong>
├ • Name ↬ (<code>$fullName</code>)
├ • Username ↬ (<code>$username</code>)
├ • UserID ↬ (<code>$id</code>)
├ • Private ↬ ($isPrivate)
├ • Verified ↬ ($is_verified)
├ • Post ↬ (<code>$mediaCount</code>)
├ • Followers ↬ (<code>$followedByCount</code>)
├ • Following ↬ (<code>$followCount</code>)
├ • Biography ↬ (<code>$biography</code>)
├ • Current ChatID ↬ (<code>$peer</code>)
├ • Developer ↬ (@DevSiNo $💠)
├ • ┅┅━━━━ 𖣫 ━━━━┅┅ •
</strong>";
$this->del($msgId,$peer);
 $this->messages->sendMedia(
peer: $peer,
media: [
        '_' => 'inputMediaUploadedPhoto',
        'file' => "$gg"
    ],
message:$inforamtion,
parse_mode:"html"
);
}}
if (preg_match('/^[\#\!\.\/]?(unEemoji)$/i', $text)) {
if (isset($replyTo)) {
if ($type == 'channel' || $type == 'supergroup') {
$rpf = yield $this->channels->getMessages(['channel' => $peer, 'id' => [$replyTo]])['users'][0];
$rpfi = $rpf['id'];
$rpfn = $rpf['first_name'];
} else {
$rpf = yield $this->messages->getMessages(['channel' => $peer, 'id' => [$replyTo]])['users'][0];
$rpfi = $rpf['id'];
$rpfn = $rpf['first_name'];
}
}else{
$rpfn=$this->getInfo($peer)['User']['first_name'];
$rpfi=$peer;
}
$s = array_search($rpfi, $DB['Eemoji']);
$s = ($s == false) ? 'no' : $s;
if ($s != 'no') {
$h = "<strong>$❌ This User </strong><a href='mention:$rpfi'>$rpfn</a><strong> UnSet Enemy </strong>";
$this->msg($update,$h,$msgId);
unset($DB['Eemoji'][$s]);
DB($DB);
}else{
$h = "<strong>$❌ This User </strong><a href='mention:$rpfi'>$rpfn</a><strong> Not Enemy :/ </strong>";
$this->msg($update,$h,$msgId);
}}

if (preg_match('/^[\#\!\.\/]?info$/si', $text)) {
if (isset($replyTo)) {
if ($type == 'channel' || $type == 'supergroup') {
$rpf = yield $this->channels->getMessages(['channel' => $peer, 'id' => [$replyTo]])['users'][0];
$id = $rpf['id'];
} else {
$rpf = yield $this->messages->getMessages(['channel' => $peer, 'id' => [$replyTo]])['users'][0];
$id = $rpf['id'];
}
}else{
$id=$peer;
}
$g="- Waiting $🔸";
$this->edit($peer,$g,$msgId);
$GETFULLINFO       =  $this->getFullInfo("$id");
$GETINFO =$this->getinfo("$id");
$USER  = $GETFULLINFO['User'];
$premium          = ($USER['premium'] == false)  ? "$no"   : "$yes";
$PHONE = isset($USER['phone'])         ? $USER['phone']     : '--';
$LASTNAME          = isset($USER['last_name'])     ? $USER['last_name'] : '--';
$CONTACT           = ($USER['contact']  == false)  ? "$no"   : "$yes";
$IS_BOT= ($USER['bot']      == false)  ? "$no"   : "$yes";
$SCAM  = ($USER['scam']     == false)  ? "$no"   : "$yes";
$VERIFIED          = ($USER['verified'] == false)  ? "$no"   : "$yes";
$SUPPORT           = ($USER['support']  == false)  ? "$no"   : "$yes";
$FULL  = $GETFULLINFO['full'];
$n     = $USER['first_name'];
$iD    = $FULL['id'];
$bio   = isset($FULL['about'])    ? $FULL['about']          : "Haven't";
$un    = isset($USER['username']) ? $USER['username']       : "Haven't";
$profile           = isset($FULL['profile_photo'])        ? $FULL['profile_photo']  : 'havent';
$status= isset($GETFULLINFO['User']['status']['_'])       ? substr($GETFULLINFO['User']['status']['_'], 10) : '--';
$last_seen         = isset($GETINFO['User']['status']['was_online']) ? $GETINFO['User']['status']['was_online']      : '--';
if ($last_seen != '--') {
    $last_seen = date('H:i:s', $last_seen);
}
$common_chats      = isset($FULL['common_chats_count'])  ? $FULL['common_chats_count']  : '--';
$IS_BLOCKED        = ($FULL['blocked'] == false) ? "$no" : "$yes";
$ALLOW_CALLS       = ($FULL['phone_calls_available'] == false) ? "$no" : "$yes";
$ALLOW_VID_CALLS   = ($FULL['video_calls_available'] == false) ? "$no" : "$yes";
$photos=  $this->photos->getUserPhotos(['user_id' => $iD, 'offset' => 0, 'max_id' => 0, 'limit' => 0]);
$pic_counts        = isset($photos['photos']) ? count($photos['photos']) : '0';

$inforamtion="
<strong>├ • User information OF</strong> <a href='mention:$id'>$n</a>$👑
<strong>├ • Name ↬ (<code>$n</code>)
├ • LastName ↬ (<code>$LASTNAME</code>)
├ • UserID ↬ (<code>$iD</code>)
├ • Phone ↬ (<code>$PHONE</code>)
├ • Your Contact ↬ ($CONTACT)
├ • Is Bot ↬ ($IS_BOT)
├ • Scam ↬ ($SCAM)
├ • Verified ↬ ($VERIFIED)
├ • Support ↬ ($SUPPORT)
├ • Blocked ↬ ($IS_BLOCKED)
├ • Premium ↬ ($premium)
├ • Allow Calls ↬ ($ALLOW_CALLS)
├ • Allow Video Calls ↬ ($ALLOW_VID_CALLS)
├ • Username ↬ (<code>$un</code>)
├ • Bio ↬ (<code>$bio</code>)
├ • Status ↬ (<code>$status</code>)
├ • LastSeen ↬ (<code>$last_seen</code>)
├ • Profile Picture ↬ (<code>$pic_counts</code>)
├ • Common Groups ↬ (<code>$common_chats</code>)
├ • Current ChatID ↬ (<code>$peer</code>)
├ • Developer ↬ (@DevSiNo $💠)
├ • ┅┅━━━━ 𖣫 ━━━━┅┅ •</strong>";
if ($profile == 'havent') {
$this->del($msgId,$peer);
$this->send($update,$inforamtion,$msgId);
} else {
$this->del($msgId,$peer);
$profile_ID        = $profile['id'];
$profile_hash      = $profile['access_hash'];
$profile_reference = $profile['file_reference'];
$input_photo       = ['_' => "inputPhoto", 'id' => $profile_ID, 'access_hash' => $profile_hash, 'file_reference' => $profile_reference];
$input_media_photo = ['_' => "inputMediaPhoto", 'id' => $input_photo];
 $this->messages->sendMedia(
peer: $peer,
media: $input_media_photo,
message:$inforamtion,
parse_mode:"html"
);
}
}
if (preg_match('/^[\#\!\.\/]?(gpinfo)$/si', $text)) {
if ($type != 'user') {
$g="- Waiting $🔸";
$this->edit($peer,$g,$msgId);
$inf  = $this->getFullInfo($peer);
$chat = $inf['Chat'];
$is_super_group       = ($chat['megagroup'] == false)      ? "$no"                  : "$yes";
$verified             = ($chat['verified']  == false)      ? "$no"                  : "$yes";
$scam                 = ($chat['scam']      == false)      ? "$no"                  : "$yes";
$title                = $chat['title'];
$username             = isset($chat['username'])           ? $chat['username']     : '--';
$full                 = $inf['full'];
$pic                  = isset($full['chat_photo'])         ? $full['chat_photo']   : '--';
$about                = isset($full['about'])              ? $full['about']        : '--';
$members_count        = $full['participants_count'];

$x1 = "<strong>├ • Group information OF [$title]
├ • Name ↬ (<code>$title</code>)
├ • Username ↬ (<code>$username</code>)
├ • Scam ↬ (<code>$scam</code>)
├ • Verified ↬ (<code>$verified</code>)
├ • Members Count ↬ (<code>$members_count</code>)
├ • About ↬ (<code>$about</code>)
├ • ┅┅━━━━ 𖣫 ━━━━┅┅ •</strong>";
if ($pic == '--') {
$this->del($msgId,$peer);
$this->send($update,$x1,$msgId);
} else {
$this->del($msgId,$peer);
$pic_id            = $pic['id'];
$pic_hash          = $pic['access_hash'];
$pic_reference     = $pic['file_reference'];
$input_photo       = ['_' => "inputPhoto", 'id' => $pic_id, 'access_hash' => $pic_hash, 'file_reference' => $pic_reference];
$input_media_photo = ['_' => "inputMediaPhoto", 'id' => $input_photo];
 $this->messages->sendMedia(
peer: $peer,
media: $input_media_photo,
message:$x1,
parse_mode:"html");
}
}
}
if (preg_match('/^[\#\!\.\/]?(info) (.*)$/si', $text,$mm)) {
$g="- Waiting $🔸";
$this->edit($peer,$g,$msgId);
$id=$mm[2];
$GETFULLINFO       =  $this->getFullInfo("$id");
$GETINFO =$this->getinfo("$id");
$USER  = $GETFULLINFO['User'];
$premium          = ($USER['premium'] == false)  ? "$no"   : "$yes";
$PHONE = isset($USER['phone'])         ? $USER['phone']     : '--';
$LASTNAME          = isset($USER['last_name'])     ? $USER['last_name'] : '--';
$CONTACT           = ($USER['contact']  == false)  ? "$no"   : "$yes";
$IS_BOT= ($USER['bot']      == false)  ? "$no"   : "$yes";
$SCAM  = ($USER['scam']     == false)  ? "$no"   : "$yes";
$VERIFIED          = ($USER['verified'] == false)  ? "$no"   : "$yes";
$SUPPORT           = ($USER['support']  == false)  ? "$no"   : "$yes";
$FULL  = $GETFULLINFO['full'];
$n     = $USER['first_name'];
$iD    = $FULL['id'];
$bio   = isset($FULL['about'])    ? $FULL['about']          : "Haven't";
$un    = isset($USER['username']) ? $USER['username']       : "Haven't";
$profile           = isset($FULL['profile_photo'])        ? $FULL['profile_photo']  : 'havent';
$status= isset($GETFULLINFO['User']['status']['_'])       ? substr($GETFULLINFO['User']['status']['_'], 10) : '--';
$last_seen         = isset($GETINFO['User']['status']['was_online']) ? $GETINFO['User']['status']['was_online']      : '--';
if ($last_seen != '--') {
    $last_seen = date('H:i:s', $last_seen);
}
$common_chats      = isset($FULL['common_chats_count'])  ? $FULL['common_chats_count']  : '--';
$IS_BLOCKED        = ($FULL['blocked'] == false) ? "$no" : "$yes";
$ALLOW_CALLS       = ($FULL['phone_calls_available'] == false) ? "$no" : "$yes";
$ALLOW_VID_CALLS   = ($FULL['video_calls_available'] == false) ? "$no" : "$yes";
$photos=  $this->photos->getUserPhotos(['user_id' => $iD, 'offset' => 0, 'max_id' => 0, 'limit' => 0]);
$pic_counts        = isset($photos['photos']) ? count($photos['photos']) : '0';

$inforamtion="
<strong>├ • User information OF</strong> <a href='mention:$id'>$n</a>$👑
<strong>├ • Name ↬ (<code>$n</code>)
├ • LastName ↬ (<code>$LASTNAME</code>)
├ • UserID ↬ (<code>$iD</code>)
├ • Phone ↬ (<code>$PHONE</code>)
├ • Your Contact ↬ ($CONTACT)
├ • Is Bot ↬ ($IS_BOT)
├ • Scam ↬ ($SCAM)
├ • Verified ↬ ($VERIFIED)
├ • Support ↬ ($SUPPORT)
├ • Blocked ↬ ($IS_BLOCKED)
├ • Premium ↬ ($premium)
├ • Allow Calls ↬ ($ALLOW_CALLS)
├ • Allow Video Calls ↬ ($ALLOW_VID_CALLS)
├ • Username ↬ (<code>$un</code>)
├ • Bio ↬ (<code>$bio</code>)
├ • Status ↬ (<code>$status</code>)
├ • LastSeen ↬ (<code>$last_seen</code>)
├ • Profile Picture ↬ (<code>$pic_counts</code>)
├ • Common Groups ↬ (<code>$common_chats</code>)
├ • Current ChatID ↬ (<code>$peer</code>)
├ • Developer ↬ (@DevSiNo $💠)
├ • ┅┅━━━━ 𖣫 ━━━━┅┅ •</strong>";
if ($profile == 'havent') {
$this->del($msgId,$peer);
$this->send($update,$inforamtion,$msgId);
} else {
$this->del($msgId,$peer);
$profile_ID        = $profile['id'];
$profile_hash      = $profile['access_hash'];
$profile_reference = $profile['file_reference'];
$input_photo       = ['_' => "inputPhoto", 'id' => $profile_ID, 'access_hash' => $profile_hash, 'file_reference' => $profile_reference];
$input_media_photo = ['_' => "inputMediaPhoto", 'id' => $input_photo];
 $this->messages->sendMedia(
peer: $peer,
media: $input_media_photo,
message:$inforamtion,
parse_mode:"html"
);
}
}

if (preg_match('/^[\#\!\.\/]?(vip)$/i', $text)) {
if ($type == 'channel' || $type == 'supergroup') {
$r = yield $this->channels->getMessages(['channel' => $peer, 'id' => [$replyTo]])['users'][0];
$id = $r['id'];
$n  = $r['first_name'];
} else {
$n=$this->getInfo($peer)['User']['first_name'];
$id=$peer;
}
$gg= $DB['PostID'][$id];
$this->del($gg,$id);

$vip ='<a href="emoji:5323651219393095687">😎</a>';
$h = "<strong>$❌ OK , User </strong><a href='mention:$id'>$n</a> $vip <strong> Now Can Send Message.</strong>";
$this->msg($update,$h,$msgId);
@$DB['spam'][$id] = 'VIP';
@$DB['PostID'][$id]="0";
DB($DB);
}
if (preg_match('/^[\#\!\.\/]?(unvip)$/i', $text)) {
if ($type == 'channel' || $type == 'supergroup') {
$r = yield $this->channels->getMessages(['channel' => $peer, 'id' => [$replyTo]])['users'][0];
$id = $r['id'];
$n  = $r['first_name'];
} else {
$n=$this->getInfo($peer)['User']['first_name'];
$id=$peer;
}
$gg= $DB['PostID'][$id];
$this->del($gg,$id);


$off = '<a href="emoji:5213147006561692829">😀</a>';
$h = "<strong>$❌ OK , Protection Enabled $off for this User </strong><a href='mention:$id'>$n</a>";
$this->msg($update,$h,$msgId);
@$DB['spam'][$id] = '0';
DB($DB);
}
if (preg_match('/^[\#\!\.\/]?(protection) (on|off)$/i', $text, $vv)) {
$h = "<strong>Protection Mode Now Is $vv[2]</strong>";
$this->msg($update,$h,$msgId);
@$DB['protection'] = $vv[2];
DB($DB);
}
if (preg_match('/^[\/]?(run)\s?(.*)$/usi', $text, $match)) {
$result   = null;
$errors   = null;
$match[2] = "return (function () use (&\$update,&\$text,&\$peer,&\$msgId,&\$replyTo,&\$type,&\$message,&\$fromId){{$match[2]}})();";
ob_start();
try {
( eval($match[2]));
$result .= ob_get_contents() . "\n";
} catch (\Throwable $e) {
$run = $e->getMessage() . PHP_EOL . "Line :" . $e->getLine();
} catch(\Exception $e) {
$run = $e->getMessage() . PHP_EOL . "Line :" . $e->getLine();
} catch(ParseError $e) {
$run = $e->getMessage() . PHP_EOL . "Line :" . $e->getLine();
} catch(FatalError $e) {
$run = $e->getMessage() . PHP_EOL . "Line :" . $e->getLine();
}
ob_end_clean();
if (empty($result)) {
$this->msg($update,"No Results ...\nError:\n" . strip_tags($run) . "\n",$msgId);
return;
}
$errors = !empty($errors) ? "\nErrors :\n$errors" : null;
$answer = "Results : \n" . $result . $errors;
$this->msg($update,"$answer",$msgId);
}

if (preg_match('/^[\#\!\.\/]?(addfilter) (.*)$/i', $text,$Match)) {
if (isset($DB['FilterList'][$peer])){
if (!in_array($Match[2] , $DB['FilterList'][$peer])) {
$DB['FilterList'][$peer][] = $Match[2];
DB($DB);
$h="• Word ( <code>$Match[2]</code>) Added in Filter List!";
$this->msg($update,$h,$msgId);
}else{
$h="• Error , Word ( <code>$Match[2]</code>) There is in Filter List!";
$this->msg($update,$h,$msgId);
}
}else{
@$DB['FilterList'][$peer][] = "$Match[2]";
DB($DB);
$h="• Word ( <code>$Match[2]</code>) Added in Filter List!";
$this->msg($update,$h,$msgId);
}
}



if (preg_match('/^[\#\!\.\/]?(delfilter) (.*)$/i', $text,$Match)) {
if (in_array($Match[2], $DB['FilterList'][$peer])) {
$ter = array_search($Match[2], $DB['FilterList'][$peer]);
unset($DB['FilterList'][$peer][$ter]);
DB($DB);
$h= "• Word ( <code>$Match[2]</code>) Deleted in Filter List!";
$this->msg($update,$h,$msgId);
}else{
$h= "• Error , Word ( <code>$Match[2]</code>) does not exist in Filter List!";
$this->msg($update,$h,$msgId);
}
}
          if($text == 'ping'){
              $this->msg($update,"$me_id ‽",$msgId);
          }          
          if($text == 'restart'){
              $this->msg($update,"Restated",$msgId);
              $this->restart();
          }
          
          
          if(preg_match("/^[\/\#\!]?(startloop)$/i", $text)){
              $this->loop->start();
              $msg = "Loop Started";
              $this->msg($update,$msg,$msgId);
             }elseif(preg_match("/^[\/\#\!]?(stoploop)$/i", $text)){
            $this->loop->stop();
              $msg = "Loop Stopsd";
              $this->msg($update,$msg,$msgId);
          }

          
       }
    } catch (err $e){
      $this->report($e->rpc);
      $this->logger($e);
    }
    }
    
     protected function send($peer, string $text, int $msgId=null)
        {
           return $this->messages->sendMessage(
                peer:$peer,
                message:$text,
                parse_mode:'html',
                reply_to_msg_id:$msgId,
            );
        }
    
     protected function edit($peer, string $text, int $msgId)
        {
           return $this->messages->editMessage(
                id:$msgId,
                peer:$peer,
                message:$text,
                parse_mode:'html'
            );
        }
      
     protected function msg(array $update, string $text, int $msgId=null)
    {     $mtd = $update['message']['out']?'edit':'send';
           return $this->{$mtd}($update, $text,$msgId);
    }
    
     protected function del(int|array $msgId, mixed $peer=null)
    {     $msgId = is_int($msgId)?[$msgId]:$msgId;
          $mtd   = is_null($peer) || isset($this->getInfo($peer)['User'])?'messages':'channels';
          return $this->{$mtd} ->deleteMessages(
            channel:$peer,
            id:$msgId,
            revoke:true
            );
    }
    
    
}

$settings = new Settings;

$settings
    ->getAppInfo()
    ->setApiId(MP::Api['id'])
    ->setApiHash(MP::Api['hash']);


MP::startAndLoop('session', $settings);
