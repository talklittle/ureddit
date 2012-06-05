        <h2>Tools</h2>
        <ul>
          <li>
            <a href="<?=PREFIX ?>/class/<?=$class->id ?>/admin">
              View this class
            </a>
          </li>
          <li>
            <a href="<?=PREFIX ?>/teachers">
              View all classes
            </a>
          </li>
          <li>
            <a href="<?=PREFIX ?>/class/<?=$class->id ?>/lectures">
              Manage lectures
            </a>
          </li>
          <li>
            <a href="<?=PREFIX ?>/class/<?=$class->id ?>/edit">
              Edit class details
            </a>
          </li>
          <li>
            <a href="<?=PREFIX ?>/class/<?=$class->id ?>/message">
              Mass message students
            </a>
          </li>
          <li>
            <a href="<?=PREFIX ?>/class/<?=$class->id ?>/stats">
              Traffic statistics
            </a>
          </li>
          <li>
            <a href="<?=PREFIX ?>/class/<?=$class->id ?>/teachers">
              Manage additional teachers
            </a>
          </li>
          <li>
            <a href="<?=PREFIX ?>/class/<?=$class->id ?>/<?=$class->seo_string($class->value) ?>">
              View public class page
            </a>
          </li>
        </ul>
