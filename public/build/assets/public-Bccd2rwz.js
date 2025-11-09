class c{constructor(){this.currentIndex=0,this.images=[],this.isOpen=!1,this.createLightbox(),this.attachEventListeners()}createLightbox(){document.body.insertAdjacentHTML("beforeend",`
            <div id="lightbox-overlay" class="lightbox-overlay" style="display: none;">
                <button id="lightbox-close" class="lightbox-close" aria-label="Fermer">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>

                <button id="lightbox-prev" class="lightbox-nav lightbox-prev" aria-label="Précédent">
                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="15 18 9 12 15 6"></polyline>
                    </svg>
                </button>

                <button id="lightbox-next" class="lightbox-nav lightbox-next" aria-label="Suivant">
                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                </button>

                <div id="lightbox-content" class="lightbox-content">
                    <img id="lightbox-image" src="" alt="" class="lightbox-image" />
                    <video id="lightbox-video" controls class="lightbox-video" style="display: none;"></video>
                    <iframe id="lightbox-iframe" class="lightbox-iframe" style="display: none;" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                </div>

                <div id="lightbox-counter" class="lightbox-counter"></div>

                <a id="lightbox-project-link" class="lightbox-project-link" style="display: none;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
                        <polyline points="15 3 21 3 21 9"></polyline>
                        <line x1="10" y1="14" x2="21" y2="3"></line>
                    </svg>
                    <span id="lightbox-project-title"></span>
                </a>
            </div>
        `)}attachEventListeners(){document.getElementById("lightbox-close").addEventListener("click",()=>this.close()),document.getElementById("lightbox-overlay").addEventListener("click",e=>{e.target.id==="lightbox-overlay"&&this.close()}),document.getElementById("lightbox-prev").addEventListener("click",()=>this.prev()),document.getElementById("lightbox-next").addEventListener("click",()=>this.next()),document.addEventListener("keydown",e=>{this.isOpen&&(e.key==="Escape"&&this.close(),e.key==="ArrowLeft"&&this.prev(),e.key==="ArrowRight"&&this.next())})}init(){const e=document.querySelectorAll("[data-lightbox]");this.images=Array.from(e).map(t=>({src:t.dataset.lightbox,type:t.dataset.lightboxType||"image",alt:t.alt||"",projectTitle:t.dataset.lightboxProjectTitle||null,projectUrl:t.dataset.lightboxProjectUrl||null})),e.forEach((t,i)=>{t.style.cursor="pointer",t.addEventListener("click",o=>{o.preventDefault(),this.open(i)})})}open(e){this.currentIndex=e,this.isOpen=!0,document.getElementById("lightbox-overlay").style.display="flex",document.body.style.overflow="hidden",this.updateContent()}close(){this.isOpen=!1,document.getElementById("lightbox-overlay").style.display="none",document.body.style.overflow="";const e=document.getElementById("lightbox-video");e.pause(),e.currentTime=0;const t=document.getElementById("lightbox-iframe");t.src=""}next(){this.currentIndex=(this.currentIndex+1)%this.images.length,this.updateContent()}prev(){this.currentIndex=(this.currentIndex-1+this.images.length)%this.images.length,this.updateContent()}updateContent(){const e=this.images[this.currentIndex],t=document.getElementById("lightbox-image"),i=document.getElementById("lightbox-video"),o=document.getElementById("lightbox-iframe"),d=document.getElementById("lightbox-counter"),l=document.getElementById("lightbox-prev"),s=document.getElementById("lightbox-next"),n=document.getElementById("lightbox-project-link"),a=document.getElementById("lightbox-project-title");d.textContent=`${this.currentIndex+1} / ${this.images.length}`,this.images.length<=1?(l.style.display="none",s.style.display="none"):(l.style.display="flex",s.style.display="flex"),e.projectUrl&&e.projectTitle?(n.href=e.projectUrl,a.textContent=e.projectTitle,n.style.display="flex"):n.style.display="none",e.type==="external-video"?(t.style.display="none",i.style.display="none",o.style.display="block",o.src=e.src):e.type==="video"?(t.style.display="none",i.style.display="block",o.style.display="none",i.src=e.src):(i.style.display="none",o.style.display="none",t.style.display="block",t.src=e.src,t.alt=e.alt)}}document.addEventListener("DOMContentLoaded",()=>{new c().init()});
