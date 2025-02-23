let sub = document.getElementById("submit")

function posts(){
   location.reload(location.href = "posts.php") 
}

sub.addEventListener("click",()=>{
    posts()
})