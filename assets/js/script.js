$(document).scroll(function(){
    var isScrolled = $(this).scrollTop() > $(".topBar").height();
    $(".topBar").toggleClass("scrolled", isScrolled); //第２引数はclassをつける条件をセット
});

function volumeToggle(button) {
    var muted = $(".previewVideo").prop('muted');
    $('.previewVideo').prop('muted', !muted);
    //buttonをクリックするたびにclassがついたり消えたりする
    $(button).find("i").toggleClass("fa-volume-mute"); //初期状態ではmuteがセットされている
    $(button).find("i").toggleClass("fa-volume-up"); //初期状態ではupはセットされていない
}

function previewEnded() {
    $('.previewVideo').toggle();
    $('.previewImage').toggle();

}

function goBack() {
    window.history.back(); //ブラウザのbackボタンを押したのと同じ挙動
}

function startHideTimer() {
    var timeout = null;

    $(document).on("mousemove", function () { //mouseを動かしたらcontrollerを出現
        clearTimeout(timeout);
        $(".watchNav").fadeIn();
        timeout = setTimeout(function () { //2秒後にcontrollerを出現させる
            $(".watchNav").fadeOut();
        }, 2000);
    });
}

function initVideo(videoId, username) { //ページが読み込まれたときにstartHideTimer,updateタイマーを実行させる
    startHideTimer();
    setStartTime(videoId, username);
    // console.log(videoId);
    // console.log(username);
    updateProgressTimer(videoId, username);

}

function updateProgressTimer(videoId, username) { //一定時間おきにdbの情報をupdateする
    addDuration(videoId, username);
    var timer;
    $("video").on("playing", function (event) { //3秒ごとに
        window.clearInterval();
        timer = window.setInterval(function () {
            updateProgress(videoId, username, event.target.currentTime); //videoタグが持っているprop
        }, 3000);


    }).on("ended", function () { //動画再生終わったらtimerを止める
        setFinished(videoId, username);
        window.clearInterval(timer);
    });
}

function addDuration(videoId, username) { //dbにinsertするrequestをする
    $.post("ajax/addDuration.php", {
        videoId: videoId,
        username: username
    }, function (data) {
        if (data !== null && data !== "") {
            alert(data);
        }
    })
}

function updateProgress(videoId, username, progress) {
    // console.log(progress);//動画の現在の秒数が表示される
    $.post("ajax/updateDuration.php", {
        videoId: videoId,
        username: username,
        progress: progress
    }, function (data) {
        if (data !== null && data !== "") {
            alert(data);
        }
    })

}

function setFinished(videoId, username) { //finishedカラムとprogressカラムをupdateする
    console.log("finished");
    $.post("ajax/setFinished.php", {
        videoId: videoId,
        username: username
    }, function (data) {
        if (data !== null && data !== "") {
            alert(data);
        }
    })

}

function setStartTime(videoId, username) { //progressカラムの値をfetchする

    $.post("ajax/getProgress.php", {
        videoId: videoId,
        username: username
    }, function (data) {
        if (isNaN(data)) { //エラーメッセージが返ってきたらtrueになる
            alert(data);
            return;
        }
        $("video").on("canplay", function () { //videoタグが再生可能な状態になるといつでも呼び出されるEventHandler
            this.currentTime = data;
            $("video").off("canplay"); //eventHandlerを解除する
        });
    })

}
function restartVideo(){
    $("video")[0].currentTime = 0; //jQueryオブジェクトにindex番号0を指定するとJavascriptオブジェクトへ変換できる
    $("video")[0].play();
    $(".upNext").fadeOut();

}
function watchVideo(videoId){ 
    window.location.href = "watch.php?id=" +videoId;
}

function showUpNext(){
    $(".upNext").fadeIn();
}

function initNote(videoId, username){
    getNote(videoId, username);
    
    $('#note').on('click',function(){
        $('.note-section').slideToggle(500);
    });
  
    $('#noteBtn').on('click',function(){
        
        updateNotes(videoId, username);
    });
    $('#deleteBtn').on('click',function(){
        removeNote(videoId,username);
    });
   
}

function updateNotes(videoId, username){
    var currentContent = $('.note-section #content').val();

    //作成
    if(storedContent == currentContent){
        alert('ノートの変更がありません。');
        return ;
    }
    addNote(videoId, username,currentContent);

}
var storedContent = "";
function getNote(videoId, username){
    $.post('ajax/getNote.php',{videoId:videoId,username:username},function(data){
        if (typeof data === 'string') { //エラーメッセージが返ってきたらtrueになる
            alert(data);
            return;
        }
        storedContent = data.content;


         $('.note-section #content').val(storedContent); 
        if(storedContent != null){
            $('#note').html('ノートを確認する');
            
            
        }else{
            $('#note').html('ノートを新しく作成する');
        }
         });
        
}
function addNote(videoId, username,content){
 $.post('ajax/addNote.php',{videoId:videoId,username:username,content:content},function(data){
    if (data !== null && data !== "") {
        alert(data);
    }else{
        alert('ノートが保存されました！！');
    }
 });
}
function removeNote(videoId, username){
    var currentContent = $('.note-section #content').val();
    if(currentContent == ""){
        return ;
    }
    if(confirm('本当に削除してよろしいですか？')){
        $.post('ajax/removeNote.php',{videoId:videoId,username:username},function(data){
            if (data !== null && data !== "") {
                alert(data);
            }else{
                alert('ノートが削除されました！！');
                
                $('.note-section #content').val('');
                $('.note-section').slideToggle(500);
            }});

    }
}
