let defaultaddr = "https://citi-test.xyz";
//let defaultaddr = "https://127.0.0.1:8000";
//let defaultaddr = "https://192.168.1.29:8000"

let toMatch = [
        /Android/i,
        /webOS/i,
        /iPhone/i,
        /iPad/i,
        /iPod/i,
        /BlackBerry/i,
        /Windows Phone/i
    ];
function  detectMob() {
        return toMatch.some((toMatchItem) => {
            return navigator.userAgent.match(toMatchItem);
        });
    }
class QrCodeView
{


    constructor(canElem, camElem)
    {

        this.scaleValue = 1;
        this.camElem = camElem;
        this.canElem = canElem;
        this.context = canElem.getContext("2d");
        canElem.width = camElem.width;
        canElem.height = camElem.height;
        this.canElem.style = "margin-left: 25%;"
        if (detectMob() === true)
        {
            this.scaleValue = -1;
            this.canElem.style = "margin-left: 5%;"
        }
        let cp = {x: Math.round(canElem.width / 2), y: Math.round(canElem.height / 2)};
        let cp1 = { x: (cp.x - Math.round(cp.x / 2)) + 10, y: (cp.y - Math.round(cp.y / 2)) + 10};
        let cp2 = { x: (cp.x + Math.round(cp.x / 2)) + 10, y: (cp.y - Math.round(cp.y / 2)) + 10};
        let cp3 = { x: (cp.x + Math.round(cp.x / 2)) + 10, y: (cp.y + Math.round(cp.y / 2)) + 10};
        let cp4 = { x: (cp.x - Math.round(cp.x / 2)) + 10, y: (cp.y + Math.round(cp.y / 2)) + 10};
        this.context.scale(-1 * this.scaleValue, 1);
        this.tcp = [cp1, cp2, cp3, cp4];
    }

    startDraw()
    {
        this.context.drawImage(this.camElem,0 ,0, this.canElem.width * (-1) * this.scaleValue , this.canElem.height);
        this.drawBorder();
    }

    drawBorder()
    {
        this.context.fillStyle = "#00000"
        // draw on y
        let c = -1;
        let len = 150;
        let large = 15;
        while (this.tcp[++c])
        {
            if (c > 1)
                this.context.fillRect(this.tcp[c].x * (-1) * this.scaleValue, this.tcp[c].y - len, large, len);
            else
                this.context.fillRect(this.tcp[c].x * (-1) * this.scaleValue, this.tcp[c].y, large, len);
        }
        //.draw on x
        let c2 = -1;
        while (++c2 < len)
        {
            this.context.fillRect((this.tcp[0].x + c2) * (-1) * this.scaleValue, this.tcp[0].y, large, large);
            this.context.fillRect((this.tcp[1].x - c2) * (-1) * this.scaleValue, this.tcp[1].y, large, large);
            this.context.fillRect((this.tcp[2].x - c2) * (-1) * this.scaleValue, this.tcp[2].y, large, large);
            this.context.fillRect((this.tcp[3].x + c2) * (-1) * this.scaleValue, this.tcp[3].y, large, large);
        }
    }


}
