/* $Id: HelloWorldExample.java,v 1.2 2001/11/29 18:27:25 remm Exp $
 *
 */

import java.io.*;
import java.text.*;
import java.util.*;
import javax.servlet.*;
import javax.servlet.http.*;
import java.net.*;
import org.jdom.*;
import org.jdom.input.SAXBuilder;
import java.lang.String;
/* import org.json.simple.* */

/**
 * The simplest possible servlet.
 *
 * @author taohu
 */

public class HelloWorldExample extends HttpServlet {


    public void doGet(HttpServletRequest request,
                      HttpServletResponse response)
        throws IOException, ServletException
    {
        ResourceBundle rb =
        ResourceBundle.getBundle("LocalStrings",request.getLocale());
        response.setContentType("text/html;charset=utf-8");
        String title = request.getParameter("title");
        String type = request.getParameter("type");
        
        //----test-------------//
        /*
	    title = "allen tom";
        type = "song";
*/
   		PrintWriter out = response.getWriter();
        try
        {
        	URL url = new URL("http://cs-server.usc.edu:36709/get_discography_hw8.php?title=" + URLEncoder.encode(title,"utf-8") + "&type=" + type);
/*         	out.println("new url"); */
        	URLConnection uc = url.openConnection();
/*         	out.println("url connection"); */
        	uc.setAllowUserInteraction(false);
        	InputStream urlStream = url.openStream();
/*         	out.println("url stream"); */

        	//use JDOM to parse XML, create JSON
        	SAXBuilder parser = new SAXBuilder();
        	Document document = (Document) parser.build(urlStream);
        	Element rootNode = document.getRootElement();
        	List list = rootNode.getChildren("result");
        	
        	if (list.size() == 0) 
        		out.println("No Discography Found...");
        	else
        	{
	        	String json_txt = "{\"results\":{\"result\":[";
	        	if(type.equals("artist"))
	        	{	
	        		for( int i = 0 ; i < list.size() ; i++)
	        		{
			        	Element node = (Element) list.get(i);
			        	
			        	json_txt += "{\"image\":\"" + node.getAttributeValue("image") + "\", ";
			           	json_txt += "\"name\":\"" + node.getAttributeValue("name").replaceAll("\"", "\\\\\"") + "\", ";
			          	json_txt += "\"genre\":\"" + node.getAttributeValue("genre").replaceAll("\"", "\\\\\"") + "\", ";
			          	json_txt += "\"year\":\"" + node.getAttributeValue("year") + "\", ";
			          	if( i == list.size() -1 )
			          		json_txt += "\"details\":\"" + node.getAttributeValue("details") + "\"} ";
			          	else	
			          		json_txt += "\"details\":\"" + node.getAttributeValue("details") + "\"}, ";  	
	 	        	}
	        	}
	        	else if(type.equals("album"))
	        	{
	        		for( int i = 0 ; i < list.size() ; i++)
		        	{
			        	Element node = (Element) list.get(i);
			        	
			        	json_txt += "{\"image\":\"" + node.getAttributeValue("image") + "\", ";
			        	json_txt += "\"title\":\"" + node.getAttributeValue("title").replaceAll("\"", "\\\\\"") + "\", ";
			           	json_txt += "\"artist\":\"" + node.getAttributeValue("artist").replaceAll("\"", "\\\\\"") + "\", ";
			          	json_txt += "\"genre\":\"" + node.getAttributeValue("genre").replaceAll("\"", "\\\\\"") + "\", ";
			          	json_txt += "\"year\":\"" + node.getAttributeValue("year") + "\", ";
			          	if ( i == list.size() -1)
			          		json_txt += "\"details\":\"" + node.getAttributeValue("details") + "\"} ";
			       		else
			   		       	json_txt += "\"details\":\"" + node.getAttributeValue("details") + "\"}, ";
	        		}
	        	}
	        	/*
//using json simple
	        	else if(type.equals("song"))
	        	{
	        		for( int i = 0 ; i < list.size() ; i++)
	        		{
			        	Element node = (Element) list.get(i);
			        	
			        	JSONArray list = new JSONArrat();
			        	list.add()
			        	
			        	
			        	json_txt += "{\"sample\":\"" + node.getAttributeValue("sample") + "\", ";
			        	json_txt += "\"title\":\"" + node.getAttributeValue("title") + "\", ";
			           	json_txt += "\"performer\":\"" + node.getAttributeValue("performer") + "\", ";
			          	json_txt += "\"composer\":\"" + node.getAttributeValue("composer") + "\", ";
			          	json_txt += "\"year\":\"" + node.getAttributeValue("year") + "\", ";
			          	if (i == list.size() -1)
			          		json_txt += "\"details\":\"" + node.getAttributeValue("details") + "\"} ";
			          	else
			          		json_txt += "\"details\":\"" + node.getAttributeValue("details") + "\"}, ";	
			          	
			          	//test
			        	json_txt =  node.getAttributeValue("title").replaceAll("&quot;","&#92&quot;");
			        	out.println(json_txt);
	        		}
	        	} 	
*/

	        	
	        	   	
	        	//escape parentheses by self
	        	else if(type.equals("song"))
	        	{
	        		for( int i = 0 ; i < list.size() ; i++)
	        		{
			        	Element node = (Element) list.get(i);
			        	
			        	json_txt += "{\"sample\":\"" + node.getAttributeValue("sample") + "\", ";
			        	json_txt += "\"title\":\"" + node.getAttributeValue("title").replaceAll("\"", "\\\\\"") + "\", ";
			           	json_txt += "\"performer\":\"" + node.getAttributeValue("performer").replaceAll("\"", "\\\\\"") + "\", ";
			          	json_txt += "\"composer\":\"" + node.getAttributeValue("composer").replaceAll("\"", "\\\\\"") + "\", ";
			          	json_txt += "\"year\":\"" + node.getAttributeValue("year") + "\", ";
			          	if (i == list.size() -1)
			          		json_txt += "\"details\":\"" + node.getAttributeValue("details") + "\"} ";
			          	else
			          		json_txt += "\"details\":\"" + node.getAttributeValue("details") + "\"}, ";	
			          		
			          	/*
json_txt = json_txt += "\"title\":\"" + node.getAttributeValue("title").replaceAll("&quot;","&#92;&quot;") + "\", ";
			          	out.println(json_txt);
*/
	        		}
	        	} 	
	        	
	        	
				//no escape
	        	/*
else if(type.equals("song"))
	        	{
	        		for( int i = 0 ; i < list.size() ; i++)
	        		{
			        	Element node = (Element) list.get(i);
			        	
			        	json_txt += "{\"sample\":\"" + node.getAttributeValue("sample") + "\", ";
			        	json_txt += "\"title\":\"" + node.getAttributeValue("title") + "\", ";
			           	json_txt += "\"performer\":\"" + node.getAttributeValue("performer") + "\", ";
			          	json_txt += "\"composer\":\"" + node.getAttributeValue("composer") + "\", ";
			          	json_txt += "\"year\":\"" + node.getAttributeValue("year") + "\", ";
			          	if (i == list.size() -1)
			          		json_txt += "\"details\":\"" + node.getAttributeValue("details") + "\"} ";
			          	else
			          		json_txt += "\"details\":\"" + node.getAttributeValue("details") + "\"}, ";	
			          	
			          	//test
			        	json_txt =  node.getAttributeValue("title").replaceAll("&quot;","&#92&quot;");
			        	out.println(json_txt);
	        		}
	        	} 	
*/
	        	json_txt += "]}}";
	        	out.println(json_txt);
	        	
        	}
        }
        
        catch (IOException io) 
        {
			out.println(io.getMessage());
		} 
		catch (JDOMException jdomex) 
		{
			out.println(jdomex.getMessage());
		}
    }
}



