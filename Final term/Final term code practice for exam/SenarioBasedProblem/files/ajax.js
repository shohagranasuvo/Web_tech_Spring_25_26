// Load all books when page loads
window.onload = function() {
    loadBooks();
};

// Function to add a book
function addBook(event) {
    event.preventDefault();
    
    // Clear previous errors
    clearErrors();
    
    let title = document.getElementById("title").value;
    let author = document.getElementById("author").value;
    let price = document.getElementById("price").value;
    let stock = document.getElementById("stock").value;
    
    let formData = "action=add&title=" + encodeURIComponent(title) + 
                   "&author=" + encodeURIComponent(author) + 
                   "&price=" + encodeURIComponent(price) + 
                   "&stock=" + encodeURIComponent(stock);
    
    let xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if(this.readyState == 4 && this.status == 200) {
            let response = JSON.parse(this.responseText);
            
            if(response.success) {
                // Show success message
                document.getElementById("message").innerHTML = 
                    '<span style="color: green;">' + response.message + '</span>';
                
                // Clear form
                document.getElementById("bookForm").reset();
                
                // Update book table
                updateBookTable(response.books);
            } else {
                // Show errors
                if(response.errors) {
                    displayErrors(response.errors);
                }
                if(response.message) {
                    document.getElementById("message").innerHTML = 
                        '<span style="color: red;">' + response.message + '</span>';
                }
            }
        }
    };
    
    xhttp.open("POST", "bookController.php", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send(formData);
}

// Function to delete a book
function deleteBook(id) {
    if(confirm("Are you sure you want to delete this book?")) {
        let formData = "action=delete&id=" + id;
        
        let xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
            if(this.readyState == 4 && this.status == 200) {
                let response = JSON.parse(this.responseText);
                
                if(response.success) {
                    // Show success message
                    document.getElementById("message").innerHTML = 
                        '<span style="color: green;">' + response.message + '</span>';
                    
                    // Update book table
                    updateBookTable(response.books);
                } else {
                    // Show error message
                    document.getElementById("message").innerHTML = 
                        '<span style="color: red;">' + response.message + '</span>';
                }
            }
        };
        
        xhttp.open("POST", "bookController.php", true);
        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhttp.send(formData);
    }
}

// Function to load all books
function loadBooks() {
    let xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if(this.readyState == 4 && this.status == 200) {
            let response = JSON.parse(this.responseText);
            
            if(response.success && response.books) {
                updateBookTable(response.books);
            }
        }
    };
    
    xhttp.open("GET", "bookController.php?action=getAll", true);
    xhttp.send();
}

// Function to update book table
function updateBookTable(books) {
    let tableBody = document.getElementById("bookTableBody");
    tableBody.innerHTML = "";
    
    if(books.length > 0) {
        books.forEach(function(book) {
            let row = tableBody.insertRow();
            
            row.innerHTML = '<td>' + book.id + '</td>' +
                          '<td>' + book.title + '</td>' +
                          '<td>' + book.author + '</td>' +
                          '<td>$' + parseFloat(book.price).toFixed(2) + '</td>' +
                          '<td>' + book.stock + '</td>' +
                          '<td><button onclick="deleteBook(' + book.id + ')" class="delete-btn">Delete</button></td>';
        });
    } else {
        let row = tableBody.insertRow();
        row.innerHTML = '<td colspan="6" style="text-align: center;">No books available</td>';
    }
}

// Function to display validation errors
function displayErrors(errors) {
    if(errors.title) {
        document.getElementById("titleError").innerHTML = errors.title;
        document.getElementById("titleError").style.color = "red";
    }
    if(errors.author) {
        document.getElementById("authorError").innerHTML = errors.author;
        document.getElementById("authorError").style.color = "red";
    }
    if(errors.price) {
        document.getElementById("priceError").innerHTML = errors.price;
        document.getElementById("priceError").style.color = "red";
    }
    if(errors.stock) {
        document.getElementById("stockError").innerHTML = errors.stock;
        document.getElementById("stockError").style.color = "red";
    }
}

// Function to clear errors
function clearErrors() {
    document.getElementById("titleError").innerHTML = "";
    document.getElementById("authorError").innerHTML = "";
    document.getElementById("priceError").innerHTML = "";
    document.getElementById("stockError").innerHTML = "";
    document.getElementById("message").innerHTML = "";
}
