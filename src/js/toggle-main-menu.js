var menu = document.getElementById('nav-main');
var buttons = document.getElementsByClassName('toggle-main-menu');

var toggleMenu = function() {
	if (menu.classList.contains('active')) {
		menu.classList.remove('active');
		menu.setAttribute('aria-hidden', true);
	} else {
		menu.classList.add('active');
		menu.setAttribute('aria-hidden', false);
	}
}

// add click event to buttons
for (var i=0;i < buttons.length;i++) {
	buttons[i].addEventListener('click', function() {
		toggleMenu();
	});
}