/**
 * Signature Canvas Handler
 * Digunakan untuk menangkap tanda tangan di canvas
 */

class SignatureCanvas {
  constructor(canvasId, clearBtnId) {
    this.canvas = document.getElementById(canvasId);
    this.clearBtn = document.getElementById(clearBtnId);

    if (!this.canvas) {
      console.error("Canvas not found: " + canvasId);
      return;
    }

    this.ctx = this.canvas.getContext("2d");
    this.isDrawing = false;
    this.hasDrawn = false;

    this.setupCanvas();
    this.attachEvents();
  }

  setupCanvas() {
    // Set canvas size
    const rect = this.canvas.getBoundingClientRect();
    this.canvas.width = rect.width;
    this.canvas.height = rect.height;

    // Set drawing style
    this.ctx.strokeStyle = "#000";
    this.ctx.lineWidth = 2;
    this.ctx.lineCap = "round";
    this.ctx.lineJoin = "round";
  }

  attachEvents() {
    // Mouse events
    this.canvas.addEventListener("mousedown", this.startDrawing.bind(this));
    this.canvas.addEventListener("mousemove", this.draw.bind(this));
    this.canvas.addEventListener("mouseup", this.stopDrawing.bind(this));
    this.canvas.addEventListener("mouseout", this.stopDrawing.bind(this));

    // Touch events for mobile
    this.canvas.addEventListener(
      "touchstart",
      this.handleTouchStart.bind(this),
    );
    this.canvas.addEventListener("touchmove", this.handleTouchMove.bind(this));
    this.canvas.addEventListener("touchend", this.stopDrawing.bind(this));

    // Clear button
    if (this.clearBtn) {
      this.clearBtn.addEventListener("click", this.clear.bind(this));
    }

    // Prevent scrolling when touching canvas
    this.canvas.addEventListener("touchstart", (e) => e.preventDefault());
    this.canvas.addEventListener("touchmove", (e) => e.preventDefault());
  }

  getPosition(e) {
    const rect = this.canvas.getBoundingClientRect();
    return {
      x: e.clientX - rect.left,
      y: e.clientY - rect.top,
    };
  }

  getTouchPosition(e) {
    const rect = this.canvas.getBoundingClientRect();
    const touch = e.touches[0];
    return {
      x: touch.clientX - rect.left,
      y: touch.clientY - rect.top,
    };
  }

  startDrawing(e) {
    this.isDrawing = true;
    const pos = this.getPosition(e);
    this.ctx.beginPath();
    this.ctx.moveTo(pos.x, pos.y);
    this.hasDrawn = true;
  }

  draw(e) {
    if (!this.isDrawing) return;

    const pos = this.getPosition(e);
    this.ctx.lineTo(pos.x, pos.y);
    this.ctx.stroke();
  }

  stopDrawing() {
    this.isDrawing = false;
  }

  handleTouchStart(e) {
    e.preventDefault();
    this.isDrawing = true;
    const pos = this.getTouchPosition(e);
    this.ctx.beginPath();
    this.ctx.moveTo(pos.x, pos.y);
    this.hasDrawn = true;
  }

  handleTouchMove(e) {
    e.preventDefault();
    if (!this.isDrawing) return;

    const pos = this.getTouchPosition(e);
    this.ctx.lineTo(pos.x, pos.y);
    this.ctx.stroke();
  }

  clear() {
    this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);
    this.hasDrawn = false;
  }

  isEmpty() {
    return !this.hasDrawn;
  }

  getDataURL() {
    return this.canvas.toDataURL("image/png");
  }

  loadFromDataURL(dataURL) {
    const img = new Image();
    img.onload = () => {
      this.ctx.drawImage(img, 0, 0);
      this.hasDrawn = true;
    };
    img.src = dataURL;
  }
}
