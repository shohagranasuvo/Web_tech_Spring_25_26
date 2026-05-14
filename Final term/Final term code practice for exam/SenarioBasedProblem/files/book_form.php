<!DOCTYPE html>
<html>
<head>
    <title>Bookshelf Management System</title>
    <script src="ajax.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f4f4f4;
        }
        
        h1 {
            color: #333;
        }
        
        .container {
            max-width: 1000px;
            margin: 0 auto;
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        
        .form-section {
            margin-bottom: 30px;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 5px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        
        table th, table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        table th {
            background-color: #4CAF50;
            color: white;
        }
        
        table tr:hover {
            background-color: #f5f5f5;
        }
        
        label {
            display: inline-block;
            width: 100px;
            font-weight: bold;
        }
        
        input[type="text"],
        input[type="number"] {
            padding: 8px;
            width: 250px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        
        input[type="submit"],
        .delete-btn {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }
        
        input[type="submit"]:hover {
            background-color: #45a049;
        }
        
        .delete-btn {
            background-color: #f44336;
        }
        
        .delete-btn:hover {
            background-color: #da190b;
        }
        
        .error {
            color: red;
            font-size: 12px;
            margin-left: 105px;
        }
        
        #message {
            margin: 10px 0;
            padding: 10px;
            text-align: center;
            font-weight: bold;
        }
        
        .form-row {
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Bookshelf Management System</h1>
        
        <div id="message"></div>
        
        <div class="form-section">
            <h2>Add New Book</h2>
            <form id="bookForm" onsubmit="addBook(event)">
                <div class="form-row">
                    <label for="title">Title:</label>
                    <input type="text" id="title" name="title">
                    <div id="titleError" class="error"></div>
                </div>
                
                <div class="form-row">
                    <label for="author">Author:</label>
                    <input type="text" id="author" name="author">
                    <div id="authorError" class="error"></div>
                </div>
                
                <div class="form-row">
                    <label for="price">Price:</label>
                    <input type="text" id="price" name="price">
                    <div id="priceError" class="error"></div>
                </div>
                
                <div class="form-row">
                    <label for="stock">Stock:</label>
                    <input type="text" id="stock" name="stock">
                    <div id="stockError" class="error"></div>
                </div>
                
                <div class="form-row">
                    <input type="submit" value="Add Book">
                </div>
            </form>
        </div>
        
        <div class="books-section">
            <h2>All Books</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="bookTableBody">
                    <tr>
                        <td colspan="6" style="text-align: center;">Loading books...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
