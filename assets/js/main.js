const message = document.querySelector('.message');
const game = {};
const output = document.querySelector('.que');
const nx = document.querySelector('.q-next');
const again = document.querySelector('.again');
const fin = document.querySelector('.fin');
nx.addEventListener('click', createQuestion);
const url = 'https://script.google.com/macros/s/AKfycby5Ov0RkNDIj5nj4GJb866Bx-z58N8vWbHmD3HV3-R5nVPusuI/exec';
let videoId='';
let username='';
again.addEventListener('click',function(){
    output.innerHTML = '<div class="spinner-border text-success" role="status"><span class="sr-only">Loading...</span></div>';
    again.classList.add("d-none");
    initQuiz(username,videoId);

});

function initQuiz(a,b){
    videoId = a;
    username = b;
    


    fetch(url).then(function (res) {
    return res.json()
    }).then(function (data) {
    
    game.total = data.data.length; // json data for game
    game.val = 0; //question we are on
    game.score = 0;
    game.arr = data.data;
    
    createQuestion();
    });}
function addResult(){
    $.post('ajax/addResults.php',{
        videoId:videoId,
        username:username,
        score:game.score},
        function (data) {
        if (data !== null && data !== "") {
            alert(data);
        }
       

    });
}
function setPassed(){
    $.post("ajax/setPassed.php",{
        videoId:videoId,
        username:username
    },function (data) {
        if (data !== null && data !== "") {
            alert(data);
        }});
}
function createQuestion() {
    nx.style.display = "none";
    if (game.val + 1 > game.total) {
        message.textContent = 'あなたのスコアは' + game.total + '問中、' + game.score + '点!';
        addResult();

        if(game.score >= 7){
            output.textContent = "合格です。お疲れさまでした。";
            again.classList.remove("d-none");
            fin.classList.remove("d-none");
            const timer = setTimeout(()=>{

                setPassed();
            },500);
            clearTimeout(timer);

        }else{
            output.textContent = "不合格です。もう一度確認しましょう。";
            again.classList.remove("d-none");
            
            
        }
        
        
       
        
    } else {
        message.textContent = '第' + (game.val + 1) + '問目  全' + game.total + '問中';
        output.innerHTML = '';
        
        let q = game.arr[game.val];
        
        const main = document.createElement('div');
        main.textContent = q.question;
        main.classList.add('q-question');
        output.appendChild(main);
        arrayRandom(q.opt);
        q.opt.forEach(function (el) {
            
            let span = document.createElement('span');
            span.textContent = el;
            span.classList.add('q-answer');
            span.classList.add('btn');
            output.appendChild(span);
            span.ans = q.answer;
            span.addEventListener('click', checker);
        })
    }
}

function arrayRandom(arr) {
    arr.sort(function () {
        return .5 - Math.random();
    })
}

function checker(e) {
    //console.log(e.target.ans);
    //console.log(this.ans);
    const selAns = document.querySelectorAll('.q-answer');
    selAns.forEach(function (ele) {
        ele.classList.remove('q-answer');
        ele.style.color = '#ddd';
        ele.removeEventListener('click', checker);
    })
    let sel = e.target;
   
    if (sel.textContent == sel.ans) {
        
        sel.style.color = 'green';
        nx.textContent = '正解!  click to next';
        game.score++;
    } else {
        sel.style.color = 'red';
        
        nx.textContent = '不正解... click to next';
    }
    game.val++;
    nx.style.display = "block";
}