// Copyright (c) 2017 Euan Ong
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the The GNU Affero General Public
// License as published by the Free Software Foundation; either
// version 3 of the License, or (at your option) any later version.
//
// You should have received a copy of the GNU Affero General Public
// License along with this library; if not, write to the Free Software
// Foundation, 51 Franklin Street, Suite 500 Boston, MA 02110-1335 USA

function ProjectViewer(Planet) {
	this.ProjectCache = Planet.GlobalPlanet.cache;
	this.PlaceholderImage = "images/planetgraphic.png";
	this.id = null;

	this.open = function(id){
		this.id = id;
		var proj = this.ProjectCache[id];
		document.getElementById("projectviewer-title").textContent = proj.ProjectName;
		document.getElementById("projectviewer-last-updated").textContent = proj.ProjectLastUpdated;
		document.getElementById("projectviewer-date").textContent = proj.ProjectCreatedDate;
		document.getElementById("projectviewer-downloads").textContent = proj.ProjectDownloads;
		document.getElementById("projectviewer-likes").textContent = proj.ProjectLikes;
		var img = proj.ProjectImage;
		if (img==""||img==null){
			img=this.PlaceholderImage;
		}
		document.getElementById("projectviewer-image").src=img;
		document.getElementById("projectviewer-description").textContent = proj.ProjectDescription;
		var tagcontainer = document.getElementById("projectviewer-tags");
		tagcontainer.innerHTML = "";
		for (var i = 0; i<proj.ProjectTags.length; i++){
			var chip = document.createElement("div");
			chip.classList.add("chipselect");
			chip.textContent = Planet.TagsManifest[proj.ProjectTags[i]].TagName;
			tagcontainer.appendChild(chip);
		}
		if (this.ProjectCache[this.id].ProjectReportedCount>0){
			document.getElementById("projectviewer-unreport").style.display = "block";
		} else {
			document.getElementById("projectviewer-unreport").style.display = "none";
		}
		$('#projectviewer').modal('open');
	};

	this.download = function(){
		var t= this;
		Planet.GlobalPlanet.getData(this.id,function(data){downloadTB(t.ProjectCache[t.id].ProjectName,data)});
	};

	this.delete = function(){
		$('#projectviewer').modal('close');
		Planet.GlobalPlanet.openDeleteModal(this.id);
	}

	this.unreport = function(){
		$('#projectviewer').modal('close');
		Planet.GlobalPlanet.openUnreportModal(this.id);
	}

	this.edit = function(){
		$('#projectviewer').modal('close');
		Planet.GlobalPlanet.downloadDataToCache(this.id,function(data){Planet.GlobalPlanet.Editor.open(this.id, false);}.bind(this));
	}

	this.init = function(){
		var t = this;
		document.getElementById("projectviewer-download-file").addEventListener('click', function (evt) {
			t.download();
		});
		document.getElementById("projectviewer-delete").addEventListener('click', function (evt) {
			t.delete();
		});
		document.getElementById("projectviewer-unreport").addEventListener('click', function (evt) {
			t.unreport();
		});
		document.getElementById("projectviewer-edit").addEventListener('click', function (evt) {
			t.edit();
		});
	};
};