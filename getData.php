<?php
$host = 'localhost';
$user = 'root';
$pass = 'root';
$dbname = 'PROGTEAM';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch categories
    $stmtCategories = $pdo->query('SELECT * FROM category');
    $categories = $stmtCategories->fetchAll(PDO::FETCH_ASSOC);

    $categoryID = isset($_GET['categoryID']) ? intval($_GET['categoryID']) : null;

    // Fetch problems for the selected category
    if ($categoryID !== null) {
        $stmtProblems = $pdo->prepare('SELECT problemID, problemname, link FROM problem WHERE categoryID = ?');
        $stmtProblems->execute([$categoryID]);
        $problems = $stmtProblems->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    die();
}



// Add Category
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['newCategory'])) {
    $newCategoryName = $_POST['newCategory'];

    try {
        // Check if the category already exists
        $stmtCheckCategory = $pdo->prepare('SELECT categoryID FROM category WHERE categoryname = ?');
        $stmtCheckCategory->execute([$newCategoryName]);
        $existingCategory = $stmtCheckCategory->fetch(PDO::FETCH_ASSOC);

        if (!$existingCategory) {
            // Add the new category
            $stmtAddCategory = $pdo->prepare('INSERT INTO category (categoryname) VALUES (?)');
            $stmtAddCategory->execute([$newCategoryName]);
            header('Location: getData.php');
            exit();
        } 
        
        else {
            $categoryErrorMessage = "Category already exists.";
        }
    }
    
    catch (PDOException $e) {
        $categoryErrorMessage = "Error adding category: " . $e->getMessage();
    }
}

// Add Problem
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['newProblemName']) && isset($_POST['newProblemLink'])) {
    $newProblemName = $_POST['newProblemName'];
    $newProblemLink = $_POST['newProblemLink'];

    try {
        // Check if the problem already exists in the selected category
        $stmtCheckProblem = $pdo->prepare('SELECT problemID FROM problem WHERE problemname = ? AND categoryID = ?');
        $stmtCheckProblem->execute([$newProblemName, $categoryID]);
        $existingProblem = $stmtCheckProblem->fetch(PDO::FETCH_ASSOC);

        if (!$existingProblem) {
            // Add the new problem
            $stmtAddProblem = $pdo->prepare('INSERT INTO problem (problemname, link, categoryID) VALUES (?, ?, ?)');
            $stmtAddProblem->execute([$newProblemName, $newProblemLink, $categoryID]);
            header('Location: getData.php');
        } 
        
        else {
            $problemErrorMessage = "Problem already exists in this category.";
        }

    } 
    catch (PDOException $e) {
        $problemErrorMessage = "Error adding problem: " . $e->getMessage();
    }
}
?>


<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Maryville College Programming Team</title>
        <link rel="stylesheet" href="stylesalter.css">

    </head>


    <body>
        <nav>
            
        <div class="navTop">
                <div class="navItem"  >
                    <a href="website.html">
                        <img width= "150" height= "100 "  src="lOGO.png"  atl ="" class="logo">
                    </a>
                
                </div>

                <div class = "navItem">
                    <div class = "search">
                        <input  type ="text" placeholder = "search..." class="searchInput">
                        <img src="https://cdn.pixabay.com/photo/2017/01/13/01/22/magnifying-glass-1976105_1280.png" width= "20" height= "20 " alt = "" class="searchIcon ">
                    </div>
                </div>
                <div class = "navItem">
                    <span class = "programmingTeam">Programming Team</span>
                </div>
            </div>       
        </nav>




        <div class="title">
                <h1>Altering Categories and Problems</h1>
            </div>

        <div class="container">
          


            <div class="box">
                <div class="column1">
                    <!-- Category Dropdown -->
                        <div class="column11">
                                <form action="" method="get">
                                <label for="category">Select a category:</label>
                                <select name="categoryID" id="category" onchange="this.form.submit()">
                                    <option value="">Select a category</option>
                                    <?php foreach ($categories as $category): ?>
                                    <option value="<?= $category['categoryID'] ?>" <?= ($categoryID !== null && $categoryID == $category['categoryID']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($category['categoryname']) ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                                 </form>

                        </div>
              


                    <!-- Display Problems with Edit and Delete options -->

                    <?php if ($categoryID !== null && isset($categories[$categoryID - 1])): ?>

                    <div class="category">
                        
                            <ul>
                                <?php if (!empty($problems)): ?>
                                    <ul>
                                        <?php foreach ($problems as $problem): ?>
                                            <li>
                                                <div class="category1">

                                                    <div class="catcol">
                                                        <a href="<?= htmlspecialchars($problem['link']) ?>"><?= htmlspecialchars($problem['problemname']) ?></a>
                                                    </div>
                                                    <!-- Edit Form -->

                                                    <div class="catcol2">
                                                            <form action="edit_problem.php" method="post" style="display: inline;">
                                                                <input type="hidden" name="problemID" value="<?= $problem['problemID'] ?>">
                                                            <!-- Replace "Edit" text with an edit icon -->
                                                                <input type="submit" value="" style="background-image: url('edit.png'); background-color: white; width: 15px; height: 15px; border: none;">

                                                            </form>
                                                            <!-- Delete Form -->
                                                            <form action="delete_problem.php" method="post" style="display: inline;">
                                                                <input type="hidden" name="problemID" value="<?= $problem['problemID'] ?>">
                                
                                                        
                                                                <input type="submit" value="" onclick="return confirm('Are you sure you want to delete this problem?')" style="background-image: url('Delete-Icon.jpg'); width: 15px; height: 17px; border: none;">

                                                            </form>

                                                    </div>

                                                </div>

                                               
                                                
                                                  
                                                

                                                

                                            </li>
                                    
                                        <?php endforeach; ?>
                                    </ul>
                                <?php else: ?>

                                    <p>No problems available for this category.</br> Would you like to add one?</p>
                                <?php endif; ?>

                            </ul>
                        
                    </div>
                    <?php endif; ?>

                    
                </div>









                <div class="column2">

                                    <!-- Category Dropdown -->
                    <div class="column21">
                        <form action="" method="get">
                            <label for="category">Select a category:</label>
                            <select name="categoryID" id="category" onchange="this.form.submit()">
                                <option value="">Select a category</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?= $category['categoryID'] ?>" <?= ($categoryID !== null && $categoryID == $category['categoryID']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($category['categoryname']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <button type="button" class="add-category-button" onclick="showAddCategoryPopup()" style="background-color: white; background-image: url('add.png'); background-repeat: no-repeat; background-size: 19px 17px; padding-left: 15px; border: none; text-indent: -9999px; overflow: hidden; width: 17px; height: 17px;">Add Category</button>


                        </form>

                                <!-- Add Category Form -->


                        <div class="add-category-popup">
                            <form action="" method="post">
                                <label for="newCategory">Add a new category:</label>
                                <input type="text" name="newCategory" required>
                                <input type="submit" value="" style="background-image: url('submit.png'); background-repeat: no-repeat; background-size: 28px 17px; width: 28px; height: 17px; border: none; cursor: pointer;">

                                <?php if (isset($categoryErrorMessage)): ?>
                                    <script>                            
                                        alert("Category already exists. Please choose a different name.");
                                    </script>
                                <?php endif; ?>
                            </form>
                        </div>

                        <script>
                            function showAddCategoryPopup() {
                                var addCategoryPopup = document.querySelector('.add-category-popup');
                                addCategoryPopup.style.display = 'block';
                            }
                        </script>

                    </div>





                    
                    <?php if ($categoryID !== null && isset($categories[$categoryID - 1])): ?>
                                <!-- Add Problem Form -->
                        <form action="" method="post">
                            <input type="hidden" name="categoryID" value="<?= $categoryID ?>">
                            <label for="newProblemName">Problem Name:</label>
                            <input type="text" name="newProblemName" required>
                            <br>
                            <label for="newProblemLink">Problem Link:</label>
                            <input type="text" name="newProblemLink" required>
                            <br>
                            <div class="buttonforproblem">
                            <input type="submit" value="Add Problem">

                            </div>
                         
                            <?php if (isset($problemSuccessMessage)): ?>
                                <p style="color: green;"><?= $problemSuccessMessage ?></p>
                            <?php elseif (isset($problemErrorMessage)): ?>
                                <p style="color: red;"><?= $problemErrorMessage ?></p>
                            <?php endif; ?>
                        </form>
                    <?php endif; ?>



                </div>

            </div>

        


            
        </div>



        </div>
    </body>



    <footer>
            <div class = "footerLeft">
                <div class="footerMenu">
                    <h1 class="fMenuTitle">About us</h1>
                    <ul class="fList">
                    <li class="fListItem"><a href="https://www.maryvillecollege.edu/">Maryville College</a></li>

                    
                        <li class="fListItem">MC Programming Team </li>
                        <li class="fListItem">Career</li>
                        <li class="fListItem">Affiliates</li>
                        <li class="fListItem">Contact</li>
                    </ul>
                </div>
                <div class="footerMenu">
                    <h1 class="fMenuTitle">Useful Links</h1>
                    <ul class="fList">
                        <li class="fListItem">Support</li>
                        <li class="fListItem"><a href="https://open.kattis.com/">Kattis</a></li>

                        <li class="fListItem">Feedback</li>
                        <li class="fListItem">Stories</li>
                    </ul>
                </div>
                
            </div>
            <div class = "footerRight">
                <div class="footerRightMenu">
                    <h1 class="fMenuTitle">Join the MC Programming Team</h1>
                    <div class="fMail">
                        <input type="text" placeholder="Your@email.com" class="fInput">
                        <button class="fButton">Join!</button>
                    </div>
                </div>
                <div class="footerRightMenu">
                    <h1 class="fMenuTitle">Follow Us</h1>
                    <div class="fIcons">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/9/95/Instagram_logo_2022.svg/1200px-Instagram_logo_2022.svg.png" alt="" class="fIcon">
                    </div>
                </div>
                <div class="footerRightMenu">
                    <span class = "copyright">@Guspedro. All rights reserved 2023</span>
                </div>
            </div>

        </footer>


</html>



