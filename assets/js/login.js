const container = document.querySelector(".container");
const btnSingIn = document.getElementById("btn-sing-in");
const btnSingUp = document.getElementById("btn-sing-up");

btnSingIn.addEventListener("click",()=>{
    container.classList.remove("toggle");
});

btnSingUp.addEventListener("click",()=>{
    container.classList.add("toggle")
});



document.querySelector('.sing-up').addEventListener('submit', function(e) {
    e.preventDefault();
    const name = document.querySelector('input[placeholder="Nombre"]').value;
    const email = document.querySelector('input[placeholder="Email"]').value;
    const password = document.querySelector('input[placeholder="Password"]').value;

    fetch('/register.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ name, email, password }),
    })
    .then(response => response.json())
    .then(data => console.log(data))
    .catch(error => console.error('Error:', error));
});

document.querySelector('.sing-in').addEventListener('submit', function(e) {
    e.preventDefault();
    const email = document.querySelector('input[placeholder="Email"]').value;
    const password = document.querySelector('input[placeholder="Password"]').value;

    fetch('/login.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ email, password }),
    })
    .then(response => response.json())
    .then(data => console.log(data))
    .catch(error => console.error('Error:', error));
});
