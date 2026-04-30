document.addEventListener("DOMContentLoaded",function(){const n=document.getElementById("url"),t=document.getElementById("fetch-preview"),a=document.getElementById("url-preview"),s=document.querySelector("form"),i=document.createElement("div");s.appendChild(i),t==null||t.addEventListener("click",async function(){const r=n.value.trim();if(r)try{t.disabled=!0,t.innerHTML=`
                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Loading...
            `;const e=await(await fetch("/posts/fetch-url",{method:"POST",headers:{"Content-Type":"application/json","X-CSRF-TOKEN":document.querySelector('meta[name="csrf-token"]').getAttribute("content")},body:JSON.stringify({url:r})})).json();if(e.success){i.innerHTML="";const o=`
                    <input type="hidden" name="url" value="${r}">
                    <input type="hidden" name="meta_title" value="${e.data.title||""}">
                    <input type="hidden" name="meta_description" value="${e.data.description||""}">
                    <input type="hidden" name="meta_image" value="${e.data.image||""}">
                `;i.innerHTML=o,a.innerHTML=`
                    <div class="border border-gray-200 dark:border-gray-600 rounded-lg overflow-hidden">
                        ${e.data.image?`
                            <img src="${e.data.image}" alt="${e.data.title}" class="w-full h-48 object-cover">
                        `:""}
                        <div class="p-4">
                            <h3 class="font-medium text-gray-900 dark:text-white">${e.data.title||"No title available"}</h3>
                            ${e.data.description?`
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">${e.data.description}</p>
                            `:""}
                            <p class="mt-1 text-xs text-gray-400">${r}</p>
                        </div>
                    </div>
                `,a.classList.remove("hidden")}else throw new Error(e.message||"Failed to fetch preview")}catch(d){console.error("Preview error:",d),alert("Failed to fetch URL preview. Please check the URL and try again.")}finally{t.disabled=!1,t.innerHTML="Preview"}}),n==null||n.addEventListener("input",function(){this.value.trim()||(a.innerHTML="",a.classList.add("hidden"),i.innerHTML="")})});
