
window.addEventListener('DOMContentLoaded', ()=>{

    const chat_con = document.querySelector('.chat_con');
    chat_con.scrollTop = chat_con.scrollHeight;

    document.getElementById('che').addEventListener('click', ()=>{
        document.getElementById('chat_box').style.display = 'none';
        if(document.getElementById('che').checked) {
            document.getElementById('che').checked = true;
            document.getElementById('add').style.display = 'block';
        } else {
            document.getElementById('add').style.display = 'none';
            document.getElementById('che').checked = false;
        }
        document.getElementById('chat').checked = false;
    })
    document.getElementById('chat').addEventListener('click', ()=>{
        document.getElementById('add').style.display = 'none';
        if(document.getElementById('chat').checked) {
            document.getElementById('chat').checked = true;
            document.getElementById('chat_box').style.display = 'block';
        } else {
            document.getElementById('chat_box').style.display = 'none';
            document.getElementById('chat').checked = false;
        }
        document.getElementById('che').checked = false;
    })

    loa();

    const add = localStorage.getItem('add');
    const chat = localStorage.getItem('chat');
    if(add == 1) {
        document.getElementById('add').style.display = 'block';
    }
    if(chat == 1) {
        document.getElementById('chat_box').style.display = 'block';
    }
    localStorage.removeItem('add');
    localStorage.removeItem('chat');
    localStorage.removeItem('back');
})
function aa() {
    history.back();
}
function sin() {
    document.getElementById('sin').checked = true;
}
function sup() {
    document.getElementById('sup').checked = true;
}
document.getElementById('chat_up').addEventListener('submit', event => {
    localStorage.setItem('chat', 1);
    const mese_text = document.getElementById('mese_text');
    if(mese_text.value.length === 0) {
        mese_text.focus();
        event.preventDefault();
    }
})
function add() {
    localStorage.setItem('add', 1);
}
const item = document.querySelectorAll('.item');
item.forEach((item, index)=> {
    item.addEventListener('click', ()=>{
        localStorage.setItem('back', index);
        localStorage.setItem('chat', 1);
    })
})
document.querySelectorAll('.itemm').forEach((item)=> {
    item.addEventListener('click', ()=>{
        localStorage.setItem('chat', 1);
    })
})
function loa() {
    const urlParams = new URLSearchParams(window.location.search);
    const chat_room = urlParams.get('chat_room');
    if(chat_room !== null) {
        const item = document.querySelectorAll('.item_a');
        item.forEach((item)=> {
            if(item.parentElement.querySelector('.add_id').textContent === chat_room) {
                item.parentElement.style.backgroundColor = '#e1e1e1';
            }
        })
    }
}
