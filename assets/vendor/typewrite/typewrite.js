class TxtType {
  constructor(el, toRotate, period = 2000) {
    this.el = el;
    this.toRotate = toRotate;
    this.period = parseInt(period, 10);
    this.loopNum = 0;
    this.txt = '';
    this.isDeleting = false;
    this.tick();
  }

  tick() {
    const i = this.loopNum % this.toRotate.length;
    const fullTxt = this.toRotate[i];
  
    this.txt = this.isDeleting
      ? fullTxt.substring(0, this.txt.length - 1)
      : fullTxt.substring(0, this.txt.length + 1);
  
    this.el.textContent = this.txt;
  
    let delta = 200 - Math.random() * 100;
    if (this.isDeleting) delta /= 2;
  
    if (!this.isDeleting && this.txt === fullTxt) {
      delta = this.period;
      this.isDeleting = true;
    } else if (this.isDeleting && this.txt === '') {
      this.isDeleting = false;
      this.loopNum++;
      delta = 500;
    }
  
    setTimeout(() => requestAnimationFrame(() => this.tick()), delta);
  }
}
