<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Test</title>
</head>
<body>

<h2>⚙️ Admin Actions</h2>
<button type="button" onclick="toggleForm('categoryForm')">➕ Add Category</button>
<button type="button" onclick="toggleForm('deadlineForm')">⏰ Set Deadline</button>

<div id="categoryForm" style="display:none; margin-top:10px; border:1px solid #ddd; padding:10px;">
  <h3>Add Category Form</h3>
</div>

<div id="deadlineForm" style="display:none; margin-top:10px; border:1px solid #ddd; padding:10px;">
  <h3>Set Deadline Form</h3>
</div>

<script>
function toggleForm(id) {
  console.log("Clicked: " + id); // Debug
  var form = document.getElementById(id);
  if (form.style.display === "none" || form.style.display === "") {
    form.style.display = "block";
  } else {
    form.style.display = "none";
  }
}
</script>

</body>
</html>
