
<form method="get" accept-charset="utf-8">
	<table>
		<tbody>
			<tr>
				<td>
					<label>Resistencia</label>
				</td>
				<td>
					<label>Tiempo</label>
				</td>
				<td>
					<label for="">Limite de Temperatura (optional)</label>
				</td>
				<td></td>
			</tr>
			<tr>
				<td>
					<input name='res' type='text' value="<?php echo isset($_GET['res']) ? $_GET['res'] : '' ?>">
				</td>
				<td>
					<input name='t' type='text' value="<?php echo isset($_GET['t']) ? $_GET['t'] : '' ?>">
				</td>
				<td>
					<input type="text" name="l" value="<?php echo isset($_GET['l']) ? $_GET['l'] : '' ?>">
				</td>
				<td>
					<input type="submit" value="Continuar">
				</td>
			</tr>
		</tbody>
	</table>
</form>
<?php if ($temperaturasCT != NULL): ?>

	<div id="chart_div"></div>
	<script type="text/javascript" src="https://www.google.com/jsapi"></script>
	    <script type="text/javascript">
	      google.load("visualization", "1", {packages:["corechart"]});
	      google.setOnLoadCallback(drawChart);
	      function drawChart() {
	        var data = google.visualization.arrayToDataTable([
	          ['Tiempo', 'Cubo Telgopor', 'Cubo Poliuretano', 'Cilindro Telgopor', 'Cilindro Poliuretano'],
			<?php foreach ($temperaturasCT as $key => $value): ?>
			  ['<?php echo $key ?>', <?php echo $value ?>, <?php echo $temperaturasCP[$key] ?>, <?php echo $temperaturasOT[$key] ?>, <?php echo $temperaturasOP[$key] ?>],
			<?php endforeach ?>
	        ]);

	        var options = {
	          title: 'Resultados',
	          height: 500,
	          fill: '#123456',
	          vAxis: {
	          	maxValue: 120
	          }
	        };

	        var chart = new google.visualization.LineChart(document.getElementById('chart_div'));
	        chart.draw(data, options);
	      };
	    </script>
	    <table>
	    	<tbody>
	    		<tr>
	    			<td>
						<?php echo "Tiempo límite de cubo de Telgopor: ".$tLimiteCT ?>
	    			</td>
	    			<td>
	    				<?php echo 'Temperatura Inicial: ' . $temperaturaInicial ?>
	    			</td>
	    		</tr>
	    		<tr>
	    			<td>
						<?php echo "Tiempo límite de cubo de Poliuretano: ".$tLimiteCP ?>
	    			</td>
	    			<td>
	    				<?php echo "Temperatura Exterior: " . $temperaturaExterior ?>
	    			</td>
	    		</tr>
	    		<tr>
	    			<td>
						<?php echo "Tiempo límite de cilindro de Telgopor: ".$tLimiteOT ?><br />
	    			</td>
	    			<td>
	    				<?php echo 'Voltaje: ' . $voltaje ?>
	    			</td>
	    		</tr>
	    		<tr>
	    			<td>
						<?php echo "Tiempo límite de cilindro de Poliuretano: ".$tLimiteOP ?><br />
	    			</td>
	    			<td>
	    				<?php echo 'Resistencia: ' . $resistencia ?>
	    			</td>
	    		</tr>
	    	</tbody>
	    </table>
		<h4>
			La mejor opción es: 
			<em>
				<?php if ($tLimiteOP == $min): ?>
					<?php echo 'Cilindro de Poliuretano' ?>
				<?php elseif($tLimiteOT == $min): ?>
					<?php echo 'Cilindro de Telgopor' ?>
				<?php elseif($tLimiteCT == $min): ?>
					<?php echo 'Cubo de Telgopor' ?>
				<?php elseif($tLimiteCP == $min): ?>
					<?php echo 'Cubo de Poliuretano' ?>
				<?php endif ?>
			</em>
		</h4>

<?php endif ?>