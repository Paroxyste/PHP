const cvs=document.getElementById('tetris');const ctx=cvs.getContext('2d');const score=document.getElementById('score');const COL=10;const ROW=20;const SQS=20;const GDC='#1C1C1A';const TETROCOLOR=[[I,'#66BAD2'],[J,'#FE8081'],[L,'#FD8816'],[O,'#FECC00'],[S,'#70C836'],[T,'#DD87CE'],[Z,'#AADF87']];function drawSquare(x,y,color){ctx.fillStyle=color;ctx.fillRect(x*SQS,y*SQS,SQS,SQS);ctx.strokeStyle='#1B1D1A';ctx.strokeRect(x*SQS,y*SQS,SQS,SQS)}
let board=[];for(r=0;r<ROW;r++){board[r]=[];for(c=0;c<COL;c++){board[r][c]=GDC}}
function drawBoard(){for(r=0;r<ROW;r++){for(c=0;c<COL;c++){drawSquare(c,r,board[r][c])}}}
drawBoard();function randomTetro(){let rand=Math.floor(Math.random()*TETROCOLOR.length);return new Piece(TETROCOLOR[rand][0],TETROCOLOR[rand][1])}
let randTetro=randomTetro();function Piece(tetromino,color){this.tetromino=tetromino;this.color=color;this.tetrominoN=0;this.activeTetro=this.tetromino[this.tetrominoN];this.x=3;this.y=-2}
Piece.prototype.fill=function(color){for(r=0;r<this.activeTetro.length;r++){for(c=0;c<this.activeTetro.length;c++){if(this.activeTetro[r][c]){drawSquare(this.x+c,this.y+r,color)}}}}
Piece.prototype.draw=function(){this.fill(this.color)}
Piece.prototype.unDraw=function(){this.fill(GDC)}
Piece.prototype.moveDown=function(){if(!this.collision(0,1,this.activeTetro)){this.unDraw();this.y++;this.draw()}else{this.lock();randTetro=randomTetro()}}
Piece.prototype.moveRight=function(){if(!this.collision(1,0,this.activeTetro)){this.unDraw();this.x++;this.draw()}}
Piece.prototype.moveLeft=function(){if(!this.collision(-1,0,this.activeTetro)){this.unDraw();this.x--;this.draw()}}
Piece.prototype.rotate=function(){let nextPattern=this.tetromino[(this.tetrominoN+1)%this.tetromino.length];let kick=0;if(this.collision(0,0,nextPattern)){if(this.x>COL/2){kick=-1}else{kick=1}}
if(!this.collision(kick,0,nextPattern)){this.unDraw();this.x+=kick;this.tetrominoN=(this.tetrominoN+1)%this.tetromino.length;this.activeTetro=this.tetromino[this.tetrominoN];this.draw()}}
let newScore=0;Piece.prototype.lock=function(){for(r=0;r<this.activeTetro.length;r++){for(c=0;c<this.activeTetro.length;c++){if(!this.activeTetro[r][c]){continue}
if(this.y+r<0){alert('GAME OVER');gameOver=!0;break}
board[this.y+r][this.x+c]=this.color}}
for(r=0;r<ROW;r++){let isRowFull=!0;for(c=0;c<COL;c++){isRowFull=isRowFull&&(board[r][c]!=GDC)}
if(isRowFull){for(y=r;y>1;y--){for(c=0;c<COL;c++){board[y][c]=board[y-1][c]}}
for(c=0;c<COL;c++){board[0][c]=GDC}
newScore+=1}}
drawBoard();score.innerHTML=newScore}
Piece.prototype.collision=function(x,y,piece){for(r=0;r<piece.length;r++){for(c=0;c<piece.length;c++){if(!piece[r][c]){continue}
let newX=this.x+c+x;let newY=this.y+r+y;if(newX<0||newX>=COL||newY>=ROW){return!0}
if(newY<0){continue}
if(board[newY][newX]!=GDC){return!0}}}
return!1}
document.addEventListener('keydown',CONTROL);function CONTROL(event){if(event.keyCode==37){randTetro.moveLeft();dropStart=Date.now()}
else if(event.keyCode==38){randTetro.rotate();dropStart=Date.now()}
else if(event.keyCode==39){randTetro.moveRight();dropStart=Date.now()}
else if(event.keyCode==40){randTetro.moveDown()}}
let dropStart=Date.now();let gameOver=!1;let speed=890;function drop(){let now=Date.now();let delta=now-dropStart;if(delta>speed){randTetro.moveDown();dropStart=Date.now()}
if(newScore==48){speed-90}else if(newScore==91){speed-80}else if(newScore==129){speed-90}else if(newScore==162){speed-80}else if(newScore==190){speed-80}else if(newScore==213){speed-90}else if(newScore==231){speed-80}else if(newScore==244){speed-80}else if(newScore==252){speed-90}else if(newScore==258){speed-30}else if(newScore==263){speed-20}else if(newScore==267){speed-10}else if(newScore==270){speed-20}else if(newScore==272){speed-20}else if(newScore==273){speed-10}
if(!gameOver){requestAnimationFrame(drop)}}
drop()