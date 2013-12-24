--
-- PostgreSQL database dump
--

SET statement_timeout = 0;
SET lock_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SET check_function_bodies = false;
SET client_min_messages = warning;

SET search_path = public, pg_catalog;

--
-- Name: htmlpages_id_seq; Type: SEQUENCE SET; Schema: public; Owner: root
--

SELECT pg_catalog.setval('htmlpages_id_seq', 2, true);


--
-- Data for Name: logs; Type: TABLE DATA; Schema: public; Owner: root
--



--
-- Name: logs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: root
--

SELECT pg_catalog.setval('logs_id_seq', 1, false);


--
-- Data for Name: users; Type: TABLE DATA; Schema: public; Owner: root
--

INSERT INTO users VALUES ('1       ', 'superadmin', 'c258d9a0675c7537c9281a057fd754fe22ed4374', NULL, NULL, NULL, NULL, '2013-12-24 19:50:58');


--
-- Data for Name: webpage_snapshots; Type: TABLE DATA; Schema: public; Owner: root
--

INSERT INTO webpage_snapshots VALUES (1, 1, NULL, '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
    <title></title>  
  </head>
  <body>
    <h1>Hello World</h1>
  
  </body>
</html>', 'h1{
 color: red;   
}', '$(document).ready(function(){
    $(''h1'').click(function(){
        $(this).css(''color'', ''blue'');
    }); 
});', '2013-12-24 20:10:53');
INSERT INTO webpage_snapshots VALUES (2, 2, 'tp1', '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
    <title></title>  
  </head>
  <body>
  
  
  </body>
</html>', 'html, body, h1, p{
    font-family: Helvetica;
    margin: 0;
}

.stopfloat{
    clear: both;
}


table{
    border-collapse:collapse;
}', '', '2013-12-24 20:33:22');


--
-- Name: webpage_snapshots_id_seq; Type: SEQUENCE SET; Schema: public; Owner: root
--

SELECT pg_catalog.setval('webpage_snapshots_id_seq', 2, true);


--
-- Data for Name: webpage_tps; Type: TABLE DATA; Schema: public; Owner: root
--

INSERT INTO webpage_tps VALUES (1, 'tp1', '1       ', 2, '2013-12-24 20:35:43', 1, '1       ', 'ok');


--
-- Data for Name: webpages; Type: TABLE DATA; Schema: public; Owner: root
--

INSERT INTO webpages VALUES (1, 'demo', '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
    <title></title>  
  </head>
  <body>
    <h1>Hello World</h1>
  
  </body>
</html>', 'h1{
 color: red;   
}', '$(document).ready(function(){
    $(''h1'').click(function(){
        $(this).css(''color'', ''blue'');
    }); 
});', '1       ', '2013-12-24 20:26:35');
INSERT INTO webpages VALUES (2, 'tp1', '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
    <title></title>  
  </head>
  <body>
  
  
  </body>
</html>', 'html, body, h1, p{
    font-family: Helvetica;
    margin: 0;
}

.stopfloat{
    clear: both;
}


table{
    border-collapse:collapse;
}', '', '1       ', '2013-12-24 20:33:22');


--
-- Name: webpages_tp_id_seq; Type: SEQUENCE SET; Schema: public; Owner: root
--

SELECT pg_catalog.setval('webpages_tp_id_seq', 1, true);


--
-- PostgreSQL database dump complete
--

