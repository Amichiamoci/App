function RandomInt(max) {
    if (max < 0) return 0;
    return Math.floor(max * Math.random());
}

export class Baloon
{
    /**
     * @param {string} imgPath 
     * @param {HTMLDivElement} container 
     * @param {string} motto 
     * @param {number} dist
     */
    constructor (imgPath, container, color, motto = null, dist = 0) {
        if (container == null)
            throw new Error("container was null!");
        
        this.path = imgPath;
        this.container = container;
        this.imgContainer = document.createElement('div');
        this.isGood = this.path !== '';
        this.dist = 0;
        if (this.path !== '')
        {
            this.img = document.createElement('img');
            this.img.src = this.path;
            this.img.alt = 'Palloncino';
            if (dist !== 0) {
                this.dist = Number(dist);
                this.img.setAttribute('style', '--dist: ' + dist.toFixed(2));
            }
            this.imgContainer.appendChild(this.img);
        }
        this.bg = color;
        this.imgContainer.style.backgroundColor = '#' + this.bg;
        this.imgContainer.className = 'circle';
        

        this.div = document.createElement('div');
        this.div.hidden = true;
        this.div.classList.add('baloon');
        this.div.appendChild(this.imgContainer);

        this.text = document.createElement('span');
        this.line = document.createElement('div');
        this.line.className = 'line';
        this.div.appendChild(this.line);
        if (motto) {
            this.text.innerHTML = motto;
        } else {
            this.line.classList.add('longer');
        }
        this.div.appendChild(this.text);


        this.imgContainer.onclick = this.div.oncontextmenu = e => {
            e.preventDefault();
            this.hit();
        }

        this.speed = {
            x: 0,
            y: 0
        }
        this.pos = {
            x: RandomInt(this.container.clientWidth * 0.8),
            y: RandomInt(this.container.clientHeight * .2) + this.container.clientHeight
        }
        this.acc = {
            x: 0,
            y: 0
        }
        this.updatePos();
        this.container.appendChild(this.div);
    }
    clone(newContainer = null) {
        if (newContainer == null)
            return new Baloon(this.path, this.container, this.bg, this.text.innerHTML, this.dist);
        return new Baloon(this.path, newContainer, this.bg, this.text.innerHTML, this.dist);
    }

    move(time) {
        if (time == null || isNaN(time))
            return;
        
        this.pos.x += this.speed.x * time;
        this.pos.y += this.speed.y * time;

        this.speed.x += this.acc.x * time;
        this.speed.y += this.acc.y * time;
    }

    hit() {
        if (this.acc.y !== 0)
        {
            return;//Already hit
        }
        this.acc.y = .4;
        this.div.classList.add('falling');
        if (this.isGood && this.onHit)
        {
            this.onHit();
        }
    }

    isOutH(screenWidth, w) {
        if (this.pos.x > screenWidth && this.speed.x >= 0)
            return true;
        if (this.pos.x + w < 0 && this.speed.x <= 0)
            return true;
        return false;
    }

    isOutTop() {
        return this.#_isOutTop(this.div.clientHeight);
    }

    #_isOutTop(h) {
        return this.pos.y + h < 0 && this.speed.y <= 0;
    }

    isOutBottom(screenHeight) {
        return this.pos.y > screenHeight && this.speed.y >= 0;
    }

    #_isVisible(screenWidth, screenHeight, w, h) {
        if (this.isOutH(screenWidth, w))
            return false;
        if (this.isOutTop(h))
            return false;
        if (this.isOutBottom(screenHeight))
            return false;
        return true;
    }

    isVisible(screenWidth, screenHeight) {
        return this.#_isVisible(screenWidth, screenHeight, this.div.clientWidth, this.div.clientHeight);
    }

    isToDelete(screenWidth, screenHeight) {
        return !this.isVisible(
            screenWidth, screenHeight);
    }

    updatePos() {
        this.div.style.top = this.pos.y + 'px';
        this.div.style.left = this.pos.x + 'px';
    }

    removeDiv() {
        try {
            this.container.removeChild(this.div);
        } catch { }
    }

    show() {
        this.div.hidden = false;
    }

    isHidden(){
        return this.div.hidden;
    }
}