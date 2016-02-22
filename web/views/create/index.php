{% extends template %}

{% block content %}
	<h1>ブログ{{name}}</h1>
	<div>
		<form method="post" action="/create{% if article.id != '' %}/{{article.id}}{% endif %}">
			<table>
				<tr>
					<th>{{title}}</th>
					<td><input type="text" name="title" value="{{article.title}}"></td>
				</tr>
				<tr>
					<th>{{content}}</th>
					<td><textarea name="content">{{article.content}}</textarea></td>
				</tr>
			</table>
			<p>
				<a href="/">戻る</a>
				<input type="submit" value="登録">
			</p>
		</form>
	</div>
{% endblock %}
