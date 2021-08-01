<?php
class JobPost
{
    public function __construct($id, $title, $description, $date, $category, $department, $location, $link)
    {
        $this->id = (string) $id;
        $this->title = (string) $title;
        $this->description = (string) $description;
        $this->date = (string) $date;
        $this->category = (string) $category;
        $this->department = (string) $department;
        $this->location = (string) $location;
        $this->link = (string) $link;
    }
}

class Redwire_Careers_Controller
{
    public function __construct()
    {
        $this->namespace = '/redwire/v1';
        $this->resource_name = 'careers';
    }

    public function register_routes()
    {
        register_rest_route($this->namespace, '/' . $this->resource_name, array(
            array(
                'methods' => 'GET',
                'callback' => array($this, 'get_items'),
            ),
            'schema' => array($this, 'get_item_schema'),
        ));
    }

    /**
     * Grabs the most recent jobs and outputs them as a rest response.
     *
     * @param WP_REST_Request $request Current request.
     */
    public function get_items($request)
    {
        $job_list = [];

        $paycor_request = wp_remote_get("https://recruitingbypaycor.com/career/CareerAtomFeed.action?clientId=8a7883d0766d99fc0176b0cd67871d57");
        $paycor_xmlstr = wp_remote_retrieve_body($paycor_request);
        $paycor_jobs = simplexml_load_string($paycor_xmlstr, null, LIBXML_NOCDATA);

        foreach ($paycor_jobs->entry as $job) {
            $job_date = DateTime::createFromFormat("Y-m-d\TH:i:s\Z", $job->updated);
            $newton = $job->children('newton', true);
            $job_list[] = new JobPost($job->id, $job->title, $job->summary, $job_date->format("Y-m-d\TH:i:sP"), $job->category['term'], $newton->department, "{$newton->location}, {$newton->state}", $job->link['href']);
        }

        $greenhouse_request = wp_remote_get("https://boards-api.greenhouse.io/v1/boards/madeinspaceeurope/jobs?content=true");
        $greenhouse_json = wp_remote_retrieve_body($greenhouse_request);
        $greenhouse_jobs = json_decode($greenhouse_json, true);

        foreach ($greenhouse_jobs['jobs'] as $job) {
            $htmlDecodedContent = (string)html_entity_decode($job['content']);
            $Link = ($job['absolute_url']);
            $job_list[] = new JobPost($job['id'], $job['title'], $htmlDecodedContent, $job['updated_at'], "Europe", $job['departments'][0]['name'], $job['location']['name'], $Link);
        }

        $data = [];

        if (empty($job_list)) {
            return rest_ensure_response($data);
        }

        foreach ($job_list as $job) {
            $response = $this->prepare_item_for_response($job, $request);
            $data[] = $this->prepare_response_for_collection($response);
        }

        return rest_ensure_response($data);
    }

    /**
     * Matches the job data to the schema we want.
     *
     * @param WP_Post $job The comment object whose response is being prepared.
     */
    public function prepare_item_for_response($job, $request)
    {
        $post_data = [];

        $schema = $this->get_item_schema($request);

        // We are also renaming the fields to more understandable names.
        if (isset($schema['properties']['id'])) {
            $post_data['id'] = $job->id;
        }

        if (isset($schema['properties']['title'])) {
            $post_data['title'] = $job->title;
        }

        if (isset($schema['properties']['description'])) {
            $post_data['description'] = $job->description;
        }

        if (isset($schema['properties']['date'])) {
            $post_data['date'] = $job->date;
        }

        if (isset($schema['properties']['category'])) {
            $post_data['category'] = $job->category;
        }

        if (isset($schema['properties']['department'])) {
            $post_data['department'] = $job->department;
        }

        if (isset($schema['properties']['location'])) {
            $post_data['location'] = $job->location;
        }

        if (isset($schema['properties']['link'])) {
            $post_data['link'] = $job->link;
        }

        return rest_ensure_response($post_data);
    }

    /**
     * Prepare a response for inserting into a collection of responses.
     *
     * This is copied from WP_REST_Controller class in the WP REST API v2 plugin.
     *
     * @param WP_REST_Response $response Response object.
     * @return array Response data, ready for insertion into collection data.
     */
    public function prepare_response_for_collection($response)
    {
        if (!($response instanceof WP_REST_Response)) {
            return $response;
        }

        $data = (array) $response->get_data();
        $server = rest_get_server();

        if (method_exists($server, 'get_compact_response_links')) {
            $links = call_user_func(array($server, 'get_compact_response_links'), $response);
        } else {
            $links = call_user_func(array($server, 'get_response_links'), $response);
        }

        if (!empty($links)) {
            $data['_links'] = $links;
        }

        return $data;
    }

    /**
     * @return array The schema for a job
     */
    public function get_item_schema()
    {
        if ($this->schema) {
            return $this->schema;
        }

        $this->schema = array(
            // This tells the spec of JSON Schema we are using which is draft 4.
            '$schema' => 'http://json-schema.org/draft-04/schema#',
            // The title property marks the identity of the resource.
            'title' => 'post',
            'type' => 'object',
            // In JSON Schema you can specify object properties in the properties attribute.
            'properties' => array(
                'id' => array(
                    'description' => esc_html__('Unique identifier for the object.', 'redwire'),
                    'type' => 'integer',
                    'readonly' => true,
                ),
                'title' => array(
                    'description' => esc_html__('The title for the object.', 'redwire'),
                    'type' => 'string',
                ),
                'description' => array(
                    'description' => esc_html__('The content for the object.', 'redwire'),
                    'type' => 'string',
                ),
                'date' => array(
                    'description' => esc_html__('The date for the object.', 'redwire'),
                    'type' => 'string',
                    'format' => 'date-time',
                ),
                'category' => array(
                    'description' => esc_html__('The category for the object.', 'redwire'),
                    'type' => 'string',
                    'format' => 'string',
                ),
                'department' => array(
                    'description' => esc_html__('The department for the object.', 'redwire'),
                    'type' => 'string',
                    'format' => 'string',
                ),
                'location' => array(
                    'description' => esc_html__('The location for the object.', 'redwire'),
                    'type' => 'string',
                    'format' => 'string',
                ),
                'link' => array(
                    'description' => esc_html__('The link for the object.', 'redwire'),
                    'type' => 'string',
                    'format' => 'string',
                ),
            )
        );

        return $this->schema;
    }
}

function redwire_rest_routes()
{
    $controller = new Redwire_Careers_Controller();
    $controller->register_routes();
}

add_action('rest_api_init', 'redwire_rest_routes');
