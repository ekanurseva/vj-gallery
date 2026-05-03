document.addEventListener('DOMContentLoaded', function () {

    /* GLOBAL STATE */
    const stage = document.getElementById('stage');
    const timeline = document.getElementById('timeline');
    const pixelsPerSecond = 50;

    let currentTime = 0;
    let isPlaying = false;
    let animationFrame = null;
    let startTimestamp = null;


    /* DRAG DARI SIDEBAR */
    interact('.slot').unset();

    interact('.content-item').draggable({
        listeners: {
            start(e){
                e.target.style.position = 'fixed';
                e.target.style.zIndex = 9999;
            },
            move(e){
                e.target.style.left = e.clientX + 'px';
                e.target.style.top  = e.clientY + 'px';
            },
            end(e){
                e.target.style.position = '';
                e.target.style.left = '';
                e.target.style.top = '';
                e.target.style.zIndex = '';
            }
        }
    });

    interact('.slot').dropzone({
        accept: '.content-item',
        overlap: 0.5,
        ondrop: handleDrop
    });


    function handleDrop(event){

        const contentId = event.relatedTarget.dataset.id;
        const type  = event.relatedTarget.dataset.type;
        const path  = event.relatedTarget.dataset.path;
        const title = event.relatedTarget.innerText;
        const slot  = event.target;
        const slotId = slot.dataset.id;

        if(type === 'audio'){
            addAudioTrack(contentId, title, path);
            return;
        }

        const div = document.createElement('div');
        div.classList.add('stage-content');
        div.dataset.contentId = contentId;
        div.dataset.slotId = slotId;
        div.dataset.title = title;
        div.dataset.type = type;
        div.dataset.startTime = 0;
        div.dataset.duration = 5;

        div.style.position = 'absolute';
        div.style.left   = slot.style.left;
        div.style.top    = slot.style.top;
        div.style.width  = slot.style.width;
        div.style.height = slot.style.height;
        div.style.zIndex = 10;

        if(type === 'image'){
            div.innerHTML = `<img src="/storage/${path}" style="width:100%;height:100%;object-fit:cover;">`;
        }

        if(type === 'video'){
            div.innerHTML = `<video src="/storage/${path}" style="width:100%;height:100%;" preload="auto"></video>`;
        }

        stage.appendChild(div);
        enableInteract(div);
        refreshLayerPanel();
        autoReindexLayers();
        renderTimeline();
    }


    /* DRAG & RESIZE STAGE CONTENT */
    function enableInteract(target){
        interact(target)
            .draggable({
                modifiers: [
                    interact.modifiers.restrictRect({
                        restriction: 'parent',
                        endOnly: true
                    })
                ],
                listeners: {
                    move(e){
                        let left = parseFloat(target.style.left) || 0;
                        let top  = parseFloat(target.style.top)  || 0;
                        target.style.left = (left + e.dx) + 'px';
                        target.style.top  = (top  + e.dy) + 'px';
                    }
                }
            })
            .resizable({
                edges: { left:true,right:true,top:true,bottom:true },
                listeners: {
                    move(e){
                        target.style.width  = e.rect.width  + 'px';
                        target.style.height = e.rect.height + 'px';
                    }
                }
            });
    }

    document.querySelectorAll('.stage-content').forEach(enableInteract);


    /* TIMELINE */
    function renderTimeline(){

        const container = document.getElementById('timelineTracks');
        container.innerHTML = '';

        const visuals = document.querySelectorAll('.stage-content');
        const audios  = document.querySelectorAll('#audioPanel .audio-item');

        const totalTracks = visuals.length + audios.length;
        const trackHeight = totalTracks * 30;
        container.style.height = trackHeight + 'px';

        let maxTime = 0;

        function createBar(el, index, color){

            const start = parseFloat(el.dataset.startTime) || 0;
            const duration = parseFloat(el.dataset.duration) || 5;
            const end = start + duration;

            if(end > maxTime) maxTime = end;

            const bar = document.createElement('div');
            bar.className = `absolute ${color} rounded text-xs px-2 flex items-center`;
            bar.style.left = (start * pixelsPerSecond) + 'px';
            bar.style.top  = (index * 30) + 'px';
            bar.style.width = (duration * pixelsPerSecond) + 'px';
            bar.style.height = '25px';
            bar.innerText = el.dataset.title;

            container.appendChild(bar);
            enableTimelineResize(bar, el);
        }

        visuals.forEach((el,i)=> createBar(el,i,'bg-blue-600'));
        audios.forEach((el,i)=> createBar(el,i+visuals.length,'bg-green-600'));

        maxTime += 2;
        container.style.width = (maxTime * pixelsPerSecond) + 'px';

        createPlayhead(container, trackHeight);
    }


    function createPlayhead(container, height){

        const playhead = document.createElement('div');
        playhead.id = 'playhead';
        playhead.style.position = 'absolute';
        playhead.style.left = (currentTime * pixelsPerSecond) + 'px';
        playhead.style.top = 0;
        playhead.style.width = '2px';
        playhead.style.height = height + 'px';
        playhead.style.background = 'red';

        container.appendChild(playhead);

        interact(playhead).draggable({
            listeners:{
                move(e){
                    let left = parseFloat(playhead.style.left) || 0;
                    left += e.dx;
                    if(left < 0) left = 0;
                    playhead.style.left = left + 'px';
                    seekTo(left / pixelsPerSecond);
                }
            }
        });
    }


    function enableTimelineResize(bar, element){

        interact(bar)
            .draggable({
                listeners:{
                    move(e){
                        let left = parseFloat(bar.style.left) || 0;
                        left += e.dx;
                        bar.style.left = left + 'px';
                        element.dataset.startTime = (left / pixelsPerSecond).toFixed(2);
                    }
                }
            })
            .resizable({
                edges:{ right:true },
                listeners:{
                    move(e){
                        bar.style.width = e.rect.width + 'px';
                        element.dataset.duration = (e.rect.width / pixelsPerSecond).toFixed(2);
                    }
                }
            });
    }


    /* PLAYBACK ENGINE */
    function updateStageVisibility(time){

        document.querySelectorAll('.stage-content').forEach(el => {

            const start = parseFloat(el.dataset.startTime) || 0;
            const duration = parseFloat(el.dataset.duration) || 5;
            const end = start + duration;

            const video = el.querySelector('video');

            if(time >= start && time <= end){

                el.style.display = 'block';

                if(video){
                    const target = time - start;
                    if(Math.abs(video.currentTime - target) > 0.3){
                        video.currentTime = target;
                    }
                    if(isPlaying && video.paused){
                        video.play().catch(()=>{});
                    }
                }

            } else {
                el.style.display = 'none';
                if(video) video.pause();
            }
        });

        document.querySelectorAll('#audioPanel audio').forEach(audio => {

            const parent = audio.closest('.audio-item');
            const start = parseFloat(parent.dataset.startTime) || 0;
            const duration = parseFloat(parent.dataset.duration) || 5;
            const end = start + duration;

            if(time >= start && time <= end){
                const target = time - start;
                if(Math.abs(audio.currentTime - target) > 0.3){
                    audio.currentTime = target;
                }
                if(isPlaying && audio.paused){
                    audio.play().catch(()=>{});
                }
            } else {
                audio.pause();
            }
        });
    }


    function playLoop(timestamp){

        if(!isPlaying) return;

        if(!startTimestamp){
            startTimestamp = timestamp - (currentTime * 1000);
        }

        currentTime = (timestamp - startTimestamp) / 1000;

        document.getElementById('currentTimeLabel').innerText =
            currentTime.toFixed(2) + 's';

        const playhead = document.getElementById('playhead');
        if(playhead){
            playhead.style.left = (currentTime * pixelsPerSecond) + 'px';
        }

        updateStageVisibility(currentTime);

        animationFrame = requestAnimationFrame(playLoop);
    }


    document.getElementById('playBtn').addEventListener('click', async ()=>{

        if(isPlaying) return;

        await unlockAllMedia();

        startTimestamp = null;
        isPlaying = true;
        animationFrame = requestAnimationFrame(playLoop);
    });

    document.getElementById('pauseBtn').addEventListener('click', ()=>{
        isPlaying = false;
        cancelAnimationFrame(animationFrame);
        document.querySelectorAll('video,audio').forEach(m=>m.pause());
    });

    function seekTo(time){

        currentTime = Math.max(0,time);

        document.getElementById('currentTimeLabel').innerText =
            currentTime.toFixed(2)+'s';

        const playhead = document.getElementById('playhead');
        if(playhead){
            playhead.style.left = (currentTime * pixelsPerSecond)+'px';
        }

        updateStageVisibility(currentTime);

        if(isPlaying){
            startTimestamp = performance.now() - (currentTime * 1000);
        }
    }

    async function unlockAllMedia(){
        const media = document.querySelectorAll('video,audio');
        for(const m of media){
            try{
                m.muted = false;
                await m.play();
                m.pause();
            }catch(e){}
        }
    }

    function refreshLayerPanel() {

        const panel = document.getElementById('layerPanel');
        panel.innerHTML = '';

        let items = Array.from(document.querySelectorAll('.stage-content'));

        // urut berdasarkan zIndex tertinggi dulu
        items.sort((a,b) =>
            parseInt(b.style.zIndex || 1) - parseInt(a.style.zIndex || 1)
        );

        items.forEach(el => {

            const div = document.createElement('div');
            div.className = 'layer-item bg-gray-700 p-2 rounded flex justify-between items-center text-sm cursor-move';
            div.dataset.contentId = el.dataset.contentId;

            let icon = '🖼️';
            if(el.querySelector('video')) icon = '🎬';

            div.innerHTML = `
                <span>${icon} ${el.dataset.title}</span>
                <button class="delete-layer text-red-400 text-xs">✕</button>
            `;

            panel.appendChild(div);
        });

        enableLayerDrag();
    }

    function autoReindexLayers(){

        const panelItems = Array.from(
            document.querySelectorAll('#layerPanel .layer-item')
        );

        panelItems.forEach((panelItem, index) => {

            const contentId = panelItem.dataset.contentId;

            const stageEl = document.querySelector(
                `.stage-content[data-content-id="${contentId}"]`
            );

            if(stageEl){
                stageEl.style.zIndex = panelItems.length - index;
            }

        });

        renderTimeline();
    }

    function addAudioTrack(contentId, title, path){

        const panel = document.getElementById('audioPanel');

        let existing = panel.querySelector(
            `.audio-item[data-content-id="${contentId}"]`
        );

        if(existing) return;

        let div = document.createElement('div');
        div.className = 'audio-item bg-gray-700 p-2 rounded cursor-move flex justify-between items-center';
        div.innerHTML = `
            <span>🎵 ${title}</span>
            <button class="delete-audio text-red-500 text-xs">✕</button>
        `;
        div.dataset.contentId = contentId;
        div.dataset.title = title;
        div.dataset.startTime = 0;
        div.dataset.duration = 10;

        const audio = document.createElement('audio');
        audio.src = '/storage/' + path;
        audio.preload = 'auto';
        audio.muted = false;
        audio.volume = 1;
        audio.style.display = 'none';
        
        audio.addEventListener('loadedmetadata', function(){
            div.dataset.duration = audio.duration.toFixed(2);
            renderTimeline();
        });

        div.appendChild(audio);

        panel.appendChild(div);

        renderTimeline();
    }

    function enableLayerDrag(){

        interact('.layer-item').draggable({
            inertia: true,
            listeners: {
                move(event){
                    event.target.style.transform =
                        `translateY(${event.dy}px)`;
                },
                end(event){
                    event.target.style.transform = '';
                }
            }
        });

        interact('#layerPanel').dropzone({
            accept: '.layer-item',
            overlap: 0.5,
            ondrop(event){

                let dragged = event.relatedTarget;

                const children = Array.from(event.target.children);
                const mouseY = event.dragEvent.clientY;

                let inserted = false;

                for(let child of children){

                    const rect = child.getBoundingClientRect();

                    if(mouseY < rect.top + rect.height/2){
                        event.target.insertBefore(dragged, child);
                        inserted = true;
                        break;
                    }
                }

                if(!inserted){
                    event.target.appendChild(dragged);
                }

                autoReindexLayers();
            }
        });
    }

    function enableAudioDrag(){

        interact('.audio-item').draggable({
            inertia: true,
            listeners: {
                move(event){
                    event.target.style.transform =
                        `translateY(${event.dy}px)`;
                },
                end(event){
                    event.target.style.transform = '';
                }
            }
        });

        interact('#audioPanel').dropzone({
            accept: '.audio-item',
            overlap: 0.5,
            ondrop(event){

                let dragged = event.relatedTarget;
                event.target.appendChild(dragged);
            }
        });
    }

    document.getElementById('saveBtn').addEventListener('click', function(){

        let audioTracks = [];

        document.querySelectorAll('#audioPanel .audio-item')
        .forEach((el, index) => {

            audioTracks.push({
                content_id: el.dataset.contentId,
                order: index + 1,
                start_time: parseFloat(el.dataset.startTime) || 0,
                duration: parseFloat(el.dataset.duration) || 10
            });

        });

        let contents = [];

        document.querySelectorAll('.stage-content').forEach(el => {

            contents.push({
                id: el.dataset.id || null,
                content_id: el.dataset.contentId,
                slot_id: el.dataset.slotId,
                pos_x: parseFloat(el.style.left),
                pos_y: parseFloat(el.style.top),
                width: parseFloat(el.style.width),
                height: parseFloat(el.style.height),
                layer_order: parseInt(el.style.zIndex) || 1,
                start_time: parseFloat(el.dataset.startTime) || 0,
                duration: parseFloat(el.dataset.duration) || 5
            });

        });

        fetch(window.editorConfig.saveUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.editorConfig.csrf
            },
            body: JSON.stringify({
                visuals: contents,
                audios: audioTracks
            })
        })
        .then(async res => {

            if (!res.ok) {
                const text = await res.text();
                console.error('SERVER ERROR:', text);
                throw new Error('Server error');
            }

            return res.json();
        })
        .then(data => {
            alert('Simulation saved!');
        })
        .catch(err => {
            console.error(err);
        });

    });

    /* DELETE STAGE CONTENT */
    function deleteStageContent(element){

        if(isPlaying){
            isPlaying = false;
            cancelAnimationFrame(animationFrame);
        }

        const video = element.querySelector('video');
        if(video){
            video.pause();
        }

        element.remove();

        refreshLayerPanel();
        autoReindexLayers();
        renderTimeline();
    }

    /* DELETE AUDIO TRACK */
    function deleteAudioTrack(element){

        if(isPlaying){
            isPlaying = false;
            cancelAnimationFrame(animationFrame);
        }

        const audio = element.querySelector('audio');
        if(audio){
            audio.pause();
        }

        element.remove();

        renderTimeline();
    }

    document.addEventListener('click', function(e){

        // DELETE DARI LAYER PANEL
        if(e.target.classList.contains('delete-layer')){

            const layerItem = e.target.closest('.layer-item');
            const contentId = layerItem.dataset.contentId;

            const stageEl = document.querySelector(
                `.stage-content[data-content-id="${contentId}"]`
            );

            if(stageEl){
                deleteStageContent(stageEl);
            }
        }

        // DELETE AUDIO
        if(e.target.classList.contains('delete-audio')){
            const el = e.target.closest('.audio-item');
            if(el){
                deleteAudioTrack(el);
            }
        }

    });

    function initSlotUpload() {

        document.querySelectorAll('.slot').forEach(slot => {

            slot.addEventListener('dragover', e => {
                e.preventDefault();
                slot.classList.add('bg-green-500/30');
            });

            slot.addEventListener('dragleave', () => {
                slot.classList.remove('bg-green-500/30');
            });

            slot.addEventListener('drop', e => {
                e.preventDefault();
                slot.classList.remove('bg-green-500/30');

                const file = e.dataTransfer.files[0];
                if (!file) return;

                uploadFileToSlot(file, slot);
            });

            // Klik untuk upload
            slot.addEventListener('click', () => {
                const input = document.createElement('input');
                input.type = 'file';
                input.onchange = () => {
                    uploadFileToSlot(input.files[0], slot);
                };
                input.click();
            });

        });
    }

    function uploadFileToSlot(file, slot) {

        const formData = new FormData();
        formData.append('file', file);

        fetch(window.editorConfig.uploadUrl, {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'X-CSRF-TOKEN': window.editorConfig.csrf
            },
            body: formData
        })
        .then(res => {
            if (!res.ok) {
                return res.text().then(text => {
                    console.error("SERVER ERROR RAW:", text);
                    throw new Error("Server error");
                });
            }
            return res.json();
        })
        .then(data => {
            renderUploadedContent(data, slot);
        })
        .catch(err => {
            console.error("Upload error:", err);
            alert("Upload gagal");
        });
    }

    function renderUploadedContent(content, slot) {

        const div = document.createElement('div');
        div.classList.add('stage-content', 'absolute', 'group');

        div.dataset.contentId = content.id;
        div.dataset.title = content.title;
        div.dataset.type = content.type;
        div.dataset.slotId = slot.dataset.id;
        div.dataset.layerOrder = 1;
        div.dataset.startTime = 0;
        div.dataset.duration = 10;

        div.style.left = slot.style.left;
        div.style.top = slot.style.top;
        div.style.width = slot.style.width;
        div.style.height = slot.style.height;
        div.style.zIndex = 1;
        div.style.border = "2px solid red";

        if (content.type === 'image') {
            div.innerHTML = `<img src="/storage/${content.path}" 
                style="width:100%;height:100%;object-fit:cover;">`;
        }

        if (content.type === 'video') {
            div.innerHTML = `<video src="/storage/${content.path}" 
                style="width:100%;height:100%;" preload="auto"></video>`;
        }

        if (content.type === 'audio') {
            div.innerHTML = `<audio src="/storage/${content.path}" preload="auto"></audio>`;
        }

        document.getElementById('stage').appendChild(div);

        refreshLayerPanel();
        renderTimeline();
    }
    
    document.getElementById('themeSelect').addEventListener('change', function(){
        fetch(`/simulations/${SIM_ID}/set-theme`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.editorConfig.csrf
            },
            body: JSON.stringify({
                theme_id: this.value
            })
        }).then(() => location.reload());
    });

    /* INIT */
    refreshLayerPanel();
    autoReindexLayers();
    renderTimeline();
    updateStageVisibility(0);
    initSlotUpload();

});