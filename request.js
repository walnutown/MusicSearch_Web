window.onload = initAll;
var xhr = false;
var typeValue

function initAll() 
{
	document.getElementById("frm").onsubmit = validation;
}

//validate the input of serach area and form the request url
function validation() 
{
	var form = 	document.getElementById("frm");
	var title = form.title.value.trim();
	document.getElementById("searchTitle").innerHTML = title.toUpperCase();
	
	if (title == "" || title == null)
	{
		document.getElementById("dynamic").innerHTML = "<h2 align='center'>Please enter something in the search box</h2>";
		form.title.focus();
		return false;
	}
	else
	{
		var origin_url = "http://cs-server.usc.edu:36710/examples/servlet/HelloWorldExample?"; 
		var type = document.getElementById('type');
		typeValue = type.options[type.selectedIndex].value;
		var url = origin_url + "title=" + title + "&type=" + typeValue;
/* 		document.getElementById("debug").innerHTML = "<p align='center'>" + url + "</p>"; */
		makeRequest(url);
		return false;
	}
}

function makeRequest(url)
{
	if (window.XMLHttpRequest) 
		xhr = new XMLHttpRequest();
	else 
	{
		if (window.ActiveXObject) 
		{
			try 
			{
				xhr = new ActiveXObject("Microsoft.XMLHTTP");
			}
			catch (e) { }
		}
	}

	if (xhr) 
	{
		xhr.open("GET", url, true);
		xhr.setRequestHeader("Connection","Close");
		xhr.setRequestHeader("Method","GET" + url + "HTTP/1.1");
		xhr.onreadystatechange = myCallback;
		xhr.send(null);
	}
	else 
		document.getElementById("dynamic").innerHTML = "Sorry, but I couldn't create an XMLHttpRequest";
}

function myCallback() 
{
	//only if xhr shows "loaded"
	if (xhr.readyState == 4) 
	{
		//only if "OK"
		if (xhr.status == 200) 
		{
			document.getElementById("dynamic").innerHTML = "<p align = 'center'>Status:\n" + xhr.status + "</p>";
			//no related information 
			if (xhr.responeseText == "No Discography Found...") 
				document.getElementById("dynamic").innerHTML = xhr.responseText;
			//with related information
			else 
			{
				var json_doc = eval("(" + xhr.responseText + ")");
				if (json_doc.error)
					document.getElementById("dynamic").innerHTML = "JSON format error";	
				else
				{
					var entry = json_doc.results.result;
					var num = json_doc.length < 5? json_doc.length:5;
					document.getElementById("number").innerHTML = num;
					
					//output table
					var html_txt = "<table align='center' border='1' cellpadding='10px' table-layout='fixed'><col width='150px'><col width='150px'><col width='150px'><col width='150px'><col width='150px'><col width='150px'>";
					
					
/* 					document.getElementById("debug").innerHTML += "<p align='center'>" + typeValue + "</p>"; */
					//output artist info
					if (typeValue == "artist")
					{
						html_txt += "<tr align='center'><th>Image</th><th>Name</th><th>Genre(s)</th><th>Year(s)</th><th>Details</th><th>Post To Facebook</th>";	
						for (var i = 0 ; i < num ; i++)
						{
							html_txt += "<tr align='center'><td>";
							if(entry[i].image != "NA")
								html_txt += "<img height='100' width='100' src='" + entry[i].image + "'></td>";
							else
								html_txt += "<img src='http://cs-server.usc.edu:36709/noImage_artist.png'></td>";
								
							html_txt += "<td>" + entry[i].name + "</td>";
							html_txt += "<td>" + entry[i].genre + "</td>";
							html_txt += "<td>" + entry[i].year + "</td>";
							html_txt += "<td><a href='" + entry[i].details + "'>details</a></td>";
							html_txt += "<td><img src = 'http://cs-server.usc.edu:36709/facebook.jpg' height='48' width='113' onclick = \"postToFeedArtist('" +  entry[i].image + "','" + entry[i].name + "','" + entry[i].genre + "','" + entry[i].year + "','" + entry[i].details + "')\"></td>";
							html_txt += "</tr>";
						}
					}
					//output album info
					else if (typeValue == "album")
					{
						html_txt += "<tr align='center'><th>Image</th><th>Title</th><th>Artist</th><th>Genre(s)</th><th>Year</th><th>Details</th><th>Post To Facebook</th>";	
						for(var i = 0; i < num; i++)
						{
							html_txt += "<tr align='center'><td>";
							if(entry[i].image != "NA")
								html_txt += "<img height='100' width='100' src='" + entry[i].image + "'></td>";
							else 
								html_txt += "<img src='http://cs-server.usc.edu:36709/noImage_album.png'></td>";
							
							html_txt += "<td>" + entry[i].title + "</td><td>";
							html_txt += entry[i].artist;
							html_txt += "</td><td>" + entry[i].genre + "</td>";
							html_txt += "<td>" + entry[i].year + "</td>";
							html_txt += "<td><a href='" + entry[i].details + "'>details</a></td>";
							html_txt += "<td><img src = 'http://cs-server.usc.edu:36709/facebook.jpg' height='48' width='113' onclick = \"postToFeedAlbum('" +  entry[i].image + "','" + entry[i].title + "','" + entry[i].artist + "','" + entry[i].genre + "','" + entry[i].year + "','" + entry[i].details + "')\"></td>";
							html_txt += "</tr>";
						}
					}
					// output song info
					else
					{
						html_txt += "<tr align='center'><th>Sample</th><th>Title</th><th>Performer</th><th>Composer(s)</th><th>Details</th><th>Post To Facebook</th>";	
						for(var i = 0; i < num; i++)
						{
							html_txt += "<tr align='center'><td>";
							if(entry[i].sample != "NA")
								html_txt += "<a href='" + entry[i].sample + "'><img src='http://cs-server.usc.edu:36709/noImage_song.png'></a></td>";
							else
								html_txt += "<img src='http://cs-server.usc.edu:36709/noImage_song.png'></td>";
								
							html_txt += "<td>" + entry[i].title + "</td><td>";
							html_txt += entry[i].performer;
							html_txt += "</td><td>";
							html_txt += entry[i].composer;
							html_txt += "<td><a href='" + entry[i].details + "'>details</a></td>";
							html_txt += "<td><img src = 'http://cs-server.usc.edu:36709/facebook.jpg' height='48' width='113' onclick = \"postToFeedArtist('" +  entry[i].sample + "','" + entry[i].title + "','" + entry[i].performer + "','" + entry[i].composer + "','" + entry[i].details + "')\"></td>";
							html_txt += "</tr>";
						}
					}	
					html_txt += "</table>";
					document.getElementById("dynamic").innerHTML = html_txt;
				}
			}
		}
		//error checking
		else 
			document.getElementById("dynamic").innerHTML = "<p align = 'center'>Error: There was a problem retriving the JSON data. Status:" + xhr.status + "</p>";
	}
}


function postToFeedArtist(image, name, genre, year, details)
{
	FB.ui(
	{
		method: 'feed',
		name: name,
		caption: ('I like' + name + 'who is active since year' + year),
		description:('Genre of Music is: ' + genre),
		link: details,
		picture: image,
		properties: {'Look at details: ' : {text : "here", href: details}}
	}
	);
}

function postToFeedAlbum(image, title, artist, genre, year, details)
{
	FB.ui(
	{
		method: 'feed',
		name: title,
		caption: ('I like' + title + 'released in ' + year),
		description:('Artist: ' + genre + 'Genre: ' + genre),
		link: details,
		picture: image,
		properties: {'Look at detials: ' : {text : "here", href: details}}
	}
	);
}

function postToFeedSong(sample, title, performer, composer, details)
{
	FB.ui(
	{
		method: 'feed',
		name: title,
		caption: ('I like' + title + 'composed by ' + composer),
		description:('Performer: ' + performer),
		link: details,
		picture: image,
		properties: {'Look at details: ' : {text : "here", href: details}}
	}
	);
}



