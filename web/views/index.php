{% extends template %}

{% block content %}
	<h1>タツ坊の部屋</h1>
	<div>
		<p>
			<a href="/create">
				<button type="button">新規投稿</button>
			</a>
		</p>
	</div>
	<hr>
{% for article in articles %}
	<div>
		<p>
			{{article.created_at}} : 「{{article.title}}」
		</p>
	</div>
	<hr>
{% endfor %}
{% endblock %}
