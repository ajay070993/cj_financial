<div class="similar_articles">
	
</div>
<script type="text/javascript">
	function searchSimilarArticles()
	{
		var titleS = $(".similar_search").val();
		if(titleS)
		{
			$.getJSON(siteurl+'admin/admin/searchSimilarArticles',{keyword:titleS},function(data){
				if(data.html)
				{
					$(".similar_articles").slideDown(600,function(){
						$(".similar_articles").html(data.html);	
					});
				}
			});
		}
	}
</script>
<style type="text/css">
	.similar_articles{
		background-color: ghostwhite;
	    border: 1px solid;
	    display: none;
	    min-height: 50px;
	}
	.similar_articles .list{
		margin: 12px;
	}
</style>