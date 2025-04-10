const wrapper = document.querySelector('.wrapper');
const bodyElement = document.querySelector('body');
const headerElement = document.querySelector('header');
const loginLink = document.querySelector('.login-link');
const registerLink = document.querySelector('.register-link');
const btnPopup = document.querySelector('.btnLogin-popup');
const iconClose = document.querySelector('.icon-close');
const front = document.querySelector('.front');

registerLink.addEventListener('click', ()=> {
    wrapper.classList.add('active');
});

loginLink.addEventListener('click', ()=> {
    wrapper.classList.remove('active');
});

btnPopup.addEventListener('click', () => {
    wrapper.classList.add('active-popup');
    bodyElement.style.setProperty('--blur-amount', '10px'); // Add blur effect
    headerElement.style.filter = 'blur(10px)';
    front.classList.add('blurred'); // Apply blur to all content except wrapper
});

iconClose.addEventListener('click', () => {
    wrapper.classList.remove('active-popup');
    headerElement.style.filter = 'blur(0px)';
    bodyElement.style.setProperty('--blur-amount', '0px'); // Remove blur effect
    front.classList.remove('blurred'); // Remove blur effect
});