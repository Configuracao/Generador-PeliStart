let x = document.getElementById("snackbar");

let menuItemBtn  = document.getElementById('menuItem'),
    menuItem = `<li>
    <a class="nav-menu__item" href="#URLHERE">
        <i class="fa-solid fa-house" /> NAME
    </a>
</li>`;

let subMenuItemBtn  = document.getElementById('subMenuItem'),
    subMenuItem = `<li>
    <a class="nav-menu__item" href="#!"><i class="fa-solid fa-film" /> Movies <i class="fa-solid fa-chevron-right sub-menu-icon" /></a>
    <ul class="sub-menu" id="sub-menu">
        <li><a href="#">Dropdown item 1</a></li>
        <li><a href="#!">Dropdown item 2</a></li>
        <li><a href="#!">Dropdown item 3</a></li>
    </ul>
</li>
`;

let videoOnlyBtn  = document.getElementById('videoOnly'),
    videoOnly = `<div class="bs-video-player">
    <h1 class="video-title">TTILE: <span>EPISODE</span></h1><!--more-->
    <ul class="player-options">
        <li class="active">Option 1</li>
        <li>Option 2</li>
        <li>Option 3</li>
    </ul>
    <ul class="bs-iframes">
        <li class="active">
            IFRAME1
        </li>
        <li>
      		IFRAME2
        </li>
        <li>
      		IFRAME3
        </li>
    </ul>

    <ul class="bs-video-controls">
        <li>
            <a href="#!" class="control">
                <i class="fa-solid fa-arrow-left"></i>
                Anterior
            </a>
        </li>
        <li>
            <a href="#" class="control">
                <i class="fa-solid fa-list"></i>
                Lista
            </a>
        </li>
        <li>
            <a href="#!" class="control">
                Siguiente
                <i class="fa-solid fa-arrow-right"></i>
            </a>
        </li>
    </ul>
</div>

<script>
const opcionesVideo=document.querySelector(".player-options").querySelectorAll("li"),videoIframes=document.querySelector(".bs-iframes").querySelectorAll("li");opcionesVideo.forEach((e,o)=>{e.addEventListener("click",()=>{opcionesVideo.forEach((e,o)=>{e.classList.remove("active"),videoIframes[o].classList.remove("active")}),e.classList.add("active"),videoIframes[o].classList.add("active")})});
</script>

`;




function toast() {
    x.className = "show";
    setTimeout(function(){ x.className = x.className.replace("show", ""); }, 3000);
}

function copyCode(item, itemCode) {
    item.addEventListener('click', () => {
        navigator.clipboard.writeText(itemCode);
        toast();
    })
}

copyCode(menuItemBtn, menuItem);
copyCode(subMenuItemBtn, subMenuItem);
copyCode(videoOnlyBtn, videoOnly);
