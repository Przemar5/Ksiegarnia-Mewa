const addProductForms = document.querySelectorAll('.book-tile__form--add-product')
const removeProductForms = document.querySelectorAll('.book-tile__form--remove-product')
const changeProductAmmountForms = document.querySelectorAll('.book-tile__btn-group--sum')
const shoppingCart = document.getElementById('shoppingCart')
const shoppingItemsCount = shoppingCart.querySelector('.navigation__shopping-items-count')


const updateAmmount = function (target, data) {
	let parsedData = JSON.parse(data)
	target.querySelector('.book-tile__btn-group--ammount').value = parsedData.ammount
	shoppingItemsCount.innerHTML = parsedData.fullAmmount
	
	if (parsedData.fullAmmount === 0) {
		shoppingItemsCount.classList.add('d-none')
	} else {
		shoppingItemsCount.classList.remove('d-none')
	}
	shoppingItemsCount.classList.add('animation--pulse')

	setTimeout(function () {
		shoppingItemsCount.classList.remove('animation--pulse')
	}, 300)
}

const handleRequest = function (form, callback) {
	let request;
	if (window.XMLHttpRequest) 		request = new XMLHttpRequest()
	else if (window.ActiveXObject) 	request = new ActiveXObject("Msxml2.XMLHTTP")
	else if (request === null) 		request = new ActiveXObject("Microsoft.XMLHTTP")
	
	request.open(form.getAttribute('method'), window.location.origin + form.getAttribute('action'))
	request.onreadystatechange = function () {
		if (request.readyState === XMLHttpRequest.DONE) {
			let status = request.status;
			if (status === 0 || (status >= 200 && status < 400)) {
				callback(form, request.responseText)
			}
		}
	}
	request.setRequestHeader('X-Requested-With', 'XMLHttpRequest')
	request.send(new FormData(form))
}

let makeRequest = function (e) {
	e.preventDefault()
	let callback = function (form, response) {
		let target = form.parentElement.querySelector('.book-tile__btn-group--sum')
		updateAmmount(target, response)
	}
	
	handleRequest(this, callback)
}

addProductForms.forEach(function (form) {
	form.addEventListener('submit', makeRequest)
})

removeProductForms.forEach(function (form) {
	form.addEventListener('submit', makeRequest)
})

changeProductAmmountForms.forEach(function (form) {
	form.addEventListener('submit', makeRequest)
})