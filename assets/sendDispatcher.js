// Send comment dispatcher
function CommentSendContract(url){
	this.send = function(comment, callback){
		if(!comment)
			return;
		
		if(callback){
			callback();
		}
	};
};

function bindABPlayerInstance(inst){
	inst.txtText.addEventListener("keydown", function(e){
		if(e && e.keyCode === 13){
			if(/^!/.test(this.value)) return; //Leave the internal commands
			if(/^\s*$/.test(this.value)) return; //Empty messages will not be sent
			var commentData = {
				"text":inst.txtText.value, 
				"mode": 1, 
				"border":true,
				"stime": inst.video ? inst.video.currrentTime : 0 
			}
			inst.cmManager.sendComment(commentData);
			if(inst.remote){
				inst.remote.send(commentData, function(){
					// Insert this into the timeline so it appears if replayed
				});
			}
			inst.txtText.value = "";
		}
	});
};
