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