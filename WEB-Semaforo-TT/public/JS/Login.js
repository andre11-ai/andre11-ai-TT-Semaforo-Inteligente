window.togglePassword = function(inputId) {
    const input = document.getElementById(inputId);
    input.type = input.type === "password" ? "text" : "password";
};

document.addEventListener("DOMContentLoaded", () => {
    const lights = ['light-green', 'light-yellow', 'light-red'];
    let currentLight = 0;

    function changeLight() {
        document.querySelectorAll('.light').forEach(el => el.classList.remove('active'));
        document.getElementById(lights[currentLight]).classList.add('active');
        currentLight = (currentLight + 1) % lights.length;
    }
    changeLight();
    setInterval(changeLight, 3500); 

    const container = document.getElementById('container');
    document.getElementById('to-register').addEventListener('click', () => container.classList.add('right-panel-active'));
    document.getElementById('to-login').addEventListener('click', () => container.classList.remove('right-panel-active'));

    document.getElementById('form-register').addEventListener('submit', (e) => {
        let valid = true;
        const email = document.getElementById('reg-email').value;
        const password = document.getElementById('reg-password').value;
        const confirmPassword = document.getElementById('reg-password-confirm').value;
        
        document.getElementById('error-reg-email').textContent = "";
        document.getElementById('error-reg-password').textContent = "";
        document.getElementById('error-reg-confirm').textContent = "";

        if (!email.endsWith('@alumno.ipn.mx')) {
            document.getElementById('error-reg-email').textContent = "Utiliza un correo institucional válido.";
            valid = false;
        }

        if (!/^(?=.*[A-Z])(?=.*\d).{8,}$/.test(password)) {
            document.getElementById('error-reg-password').textContent = "Requiere 8 caracteres, 1 mayúscula y 1 número.";
            valid = false;
        }

        if (password !== confirmPassword) {
            document.getElementById('error-reg-confirm').textContent = "Las contraseñas no coinciden.";
            valid = false;
        }

        if (!valid) e.preventDefault();
    });

    const canvas = document.getElementById('network-canvas');
    const ctx = canvas.getContext('2d');
    let particlesArray = [];
    
    function setCanvasSize() {
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;
    }
    setCanvasSize();
    window.addEventListener('resize', setCanvasSize);

    class Particle {
        constructor() {
            this.x = Math.random() * canvas.width;
            this.y = Math.random() * canvas.height;
            this.size = Math.random() * 3 + 1;
            this.speedX = (Math.random() * 1) - 0.5;
            this.speedY = (Math.random() * 1) - 0.5;
        }
        update() {
            this.x += this.speedX;
            this.y += this.speedY;
            if (this.x < 0 || this.x > canvas.width) this.speedX *= -1;
            if (this.y < 0 || this.y > canvas.height) this.speedY *= -1;
        }
        draw() {
            ctx.fillStyle = '#00ffff'; 
            ctx.beginPath();
            ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
            ctx.fill();
        }
    }

    function initParticles() {
        particlesArray = [];
        for (let i = 0; i < 100; i++) {
            particlesArray.push(new Particle());
        }
    }

    function handleParticles() {
        for (let i = 0; i < particlesArray.length; i++) {
            particlesArray[i].update();
            particlesArray[i].draw();
            for (let j = i; j < particlesArray.length; j++) {
                const dx = particlesArray[i].x - particlesArray[j].x;
                const dy = particlesArray[i].y - particlesArray[j].y;
                const distance = Math.sqrt(dx * dx + dy * dy);
                if (distance < 150) {
                    ctx.beginPath();
                    ctx.strokeStyle = `rgba(0, 255, 255, ${1 - distance/150})`;
                    ctx.lineWidth = 0.5;
                    ctx.moveTo(particlesArray[i].x, particlesArray[i].y);
                    ctx.lineTo(particlesArray[j].x, particlesArray[j].y);
                    ctx.stroke();
                }
            }
        }
    }

    function animateCanvas() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        handleParticles();
        requestAnimationFrame(animateCanvas);
    }

    initParticles();
    animateCanvas();
});